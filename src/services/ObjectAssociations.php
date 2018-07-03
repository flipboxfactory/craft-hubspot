<?php

/**
 * @copyright  Copyright (c) Flipbox Digital Limited
 * @license    https://flipboxfactory.com/software/hubspot/license
 * @link       https://www.flipboxfactory.com/software/hubspot/
 */

namespace flipbox\hubspot\services;

use Codeception\Exception\ElementNotFound;
use Craft;
use craft\base\Element;
use craft\base\ElementInterface;
use craft\errors\ElementNotFoundException;
use craft\helpers\Json;
use flipbox\craft\sortable\associations\db\SortableAssociationQueryInterface;
use flipbox\craft\sortable\associations\records\SortableAssociationInterface;
use flipbox\craft\sortable\associations\services\SortableAssociations;
use flipbox\ember\exceptions\NotFoundException;
use flipbox\ember\helpers\SiteHelper;
use flipbox\ember\services\traits\records\Accessor;
use flipbox\ember\validators\MinMaxValidator;
use flipbox\hubspot\db\ObjectAssociationQuery;
use flipbox\hubspot\fields\Objects;
use flipbox\hubspot\HubSpot;
use flipbox\hubspot\migrations\ObjectAssociation as ObjectAssociationMigration;
use flipbox\hubspot\records\ObjectAssociation;

/**
 * @author Flipbox Factory <hello@flipboxfactory.com>
 * @since 1.0.0
 *
 * @method ObjectAssociationQuery parentGetQuery($config = [])
 * @method ObjectAssociation create(array $attributes = [])
 * @method ObjectAssociation find($identifier)
 * @method ObjectAssociation get($identifier)
 * @method ObjectAssociation findByCondition($condition = [])
 * @method ObjectAssociation getByCondition($condition = [])
 * @method ObjectAssociation findByCriteria($criteria = [])
 * @method ObjectAssociation getByCriteria($criteria = [])
 * @method ObjectAssociation[] findAllByCondition($condition = [])
 * @method ObjectAssociation[] getAllByCondition($condition = [])
 * @method ObjectAssociation[] findAllByCriteria($criteria = [])
 * @method ObjectAssociation[] getAllByCriteria($criteria = [])
 */
class ObjectAssociations extends SortableAssociations
{
    use Accessor {
        getQuery as parentGetQuery;
    }

    /**
     * @inheritdoc
     */
    const SOURCE_ATTRIBUTE = ObjectAssociation::SOURCE_ATTRIBUTE;

    /**
     * @inheritdoc
     */
    const TARGET_ATTRIBUTE = ObjectAssociation::TARGET_ATTRIBUTE;

    /**
     * @inheritdoc
     * @throws \Throwable
     */
    public function init()
    {
        $settings = HubSpot::getInstance()->getSettings();
        $this->cacheDuration = $settings->associationsCacheDuration;
        $this->cacheDependency = $settings->associationsCacheDependency;

        parent::init();

        $this->ensureTableExists();
    }

    /**
     * @throws \Throwable
     */
    public function ensureTableExists()
    {
        if (!in_array(
            Craft::$app->getDb()->tablePrefix . ObjectAssociation::tableAlias(),
            Craft::$app->getDb()->getSchema()->tableNames,
            true
        )) {
            $this->createTable();
        }
    }

    /**
     * @return bool
     * @throws \Throwable
     */
    private function createTable(): bool
    {
        ob_start();
        (new ObjectAssociationMigration())->up();
        ob_end_clean();

        return true;
    }

    /**
     * @inheritdoc
     * @return ObjectAssociationQuery
     */
    public function getQuery($config = []): SortableAssociationQueryInterface
    {
        return $this->parentGetQuery($config);
    }

    /**
     * @param ElementInterface $element
     * @param Objects $field
     * @return string|null
     */
    public function findObjectIdByElement(ElementInterface $element, Objects $field)
    {
        /** @var Element $element */
        return $this->findObjectId($element->getId(), $field->id, $element->siteId);
    }

    /**
     * @noinspection PhpDocMissingThrowsInspection
     *
     * @param int $elementId
     * @param int $fieldId
     * @param int|null $siteId
     * @return null|string
     */
    public function findObjectId(int $elementId, int $fieldId, int $siteId = null)
    {
        $objectId = HubSpot::getInstance()->getObjectAssociations()->getQuery([
            'select' => ['objectId'],
            'elementId' => $elementId,
            'siteId' => SiteHelper::ensureSiteId($siteId),
            'fieldId' => $fieldId
        ])->scalar();

        return is_string($objectId) ? $objectId : null;
    }

    /**
     * @param int $elementId
     * @param int $fieldId
     * @param int|null $siteId
     * @return string
     * @throws NotFoundException
     */
    public function getObjectId(int $elementId, int $fieldId, int $siteId = null): string
    {
        $siteId = SiteHelper::ensureSiteId($siteId);

        if (null === ($objectId = $this->findObjectId($elementId, $fieldId, $siteId))) {
            throw new NotFoundException(sprintf(
                "Unable to find element with: Element Id: %s, Field Id: %s, Site Id: $%s",
                $elementId,
                $fieldId,
                $siteId
            ));
        }

        return $objectId;
    }

    /**
     * @noinspection PhpDocMissingThrowsInspection
     *
     * @param string $objectId
     * @param int $fieldId
     * @param int|null $siteId
     * @return null|string
     */
    public function findElementId(string $objectId, int $fieldId, int $siteId = null)
    {
        $elementId = HubSpot::getInstance()->getObjectAssociations()->getQuery([
            'select' => ['elementId'],
            'objectId' => $objectId,
            'siteId' => SiteHelper::ensureSiteId($siteId),
            'fieldId' => $fieldId
        ])->scalar();

        return is_string($elementId) ? $elementId : null;
    }

    /**
     * @param string $objectId
     * @param int $fieldId
     * @param int|null $siteId
     * @return string
     * @throws NotFoundException
     */
    public function getElementId(string $objectId, int $fieldId, int $siteId = null): string
    {
        $siteId = SiteHelper::ensureSiteId($siteId);

        if (null === ($elementId = $this->findElementId($objectId, $fieldId, $siteId))) {
            throw new NotFoundException(sprintf(
                "Unable to find element with: HubSpot Id: %s, Field Id: %s, Site Id: $%s",
                $objectId,
                $fieldId,
                $siteId
            ));
        }

        return $elementId;
    }

    /**
     * @param string $objectId
     * @param int $fieldId
     * @param int|null $siteId
     * @return ElementInterface|null
     */
    public function findElement(string $objectId, int $fieldId, int $siteId = null)
    {
        if (null === ($elementId = $this->findElementId($fieldId, $objectId, $siteId))) {
            return null;
        }

        return Craft::$app->getElements()->getELementById($elementId, null, $siteId);
    }

    /**
     * @param string $objectId
     * @param int $fieldId
     * @param int|null $siteId
     * @return ElementInterface
     * @throws ElementNotFoundException
     */
    public function getElement(string $objectId, int $fieldId, int $siteId = null): ElementInterface
    {
        $siteId = SiteHelper::ensureSiteId($siteId);

        if (!$element = $this->findElement($fieldId, $objectId, $siteId)) {
            throw new ElementNotFound(sprintf(
                "Unable to find element with: HubSpot Id: %s, Field Id: %s, Site Id: $%s",
                $objectId,
                $fieldId,
                $siteId
            ));
        }

        return $element;
    }

    /**
     * @inheritdoc
     * @return ObjectAssociationQuery
     */
    protected function associationQuery(
        SortableAssociationInterface $record
    ): SortableAssociationQueryInterface {
        /** @var ObjectAssociation $record */
        return $this->query(
            $record->{static::SOURCE_ATTRIBUTE},
            $record->fieldId,
            $record->siteId
        );
    }

    /**
     * @inheritdoc
     * @param ObjectAssociationQuery $query
     */
    protected function existingAssociations(
        SortableAssociationQueryInterface $query
    ): array {
        $source = $this->resolveStringAttribute($query, 'element');
        $field = $this->resolveStringAttribute($query, 'field');
        $site = $this->resolveStringAttribute($query, 'siteId');

        if ($source === null || $field === null || $site === null) {
            return [];
        }

        return $this->associations($source, $field, $site);
    }

    /**
     * @param $source
     * @param int $fieldId
     * @param int $siteId
     * @return ObjectAssociationQuery
     */
    private function query(
        $source,
        int $fieldId,
        int $siteId
    ): ObjectAssociationQuery {
        return $this->getQuery()
            ->where([
                static::SOURCE_ATTRIBUTE => $source,
                'fieldId' => $fieldId,
                'siteId' => $siteId
            ])
            ->orderBy(['sortOrder' => SORT_ASC]);
    }

    /**
     * @param $source
     * @param int $fieldId
     * @param int $siteId
     * @return array
     */
    private function associations(
        $source,
        int $fieldId,
        int $siteId
    ): array {
        return $this->query($source, $fieldId, $siteId)
            ->indexBy(static::TARGET_ATTRIBUTE)
            ->all();
    }

    /**
     * @inheritdoc
     * @param bool $validate
     * @throws \Exception
     */
    public function save(
        SortableAssociationQueryInterface $query,
        bool $validate = true
    ): bool {
        if ($validate === true && null !== ($field = $this->resolveFieldFromQuery($query))) {
            $error = '';
            (new MinMaxValidator([
                'min' => $field->min ? (int)$field->min : null,
                'max' => $field->max ? (int)$field->max : null
            ]))->validate($query, $error);

            if (!empty($error)) {
                HubSpot::error(sprintf(
                    "Hubspot Resource failed to save due to the following validation errors: '%s'",
                    Json::encode($error)
                ));
                return false;
            }
        }

        return parent::save($query);
    }

    /**
     * @param SortableAssociationQueryInterface $query
     * @return Objects|null
     */
    protected function resolveFieldFromQuery(
        SortableAssociationQueryInterface $query
    ) {
        if (null === ($fieldId = $this->resolveStringAttribute($query, 'field'))) {
            return null;
        }

        return HubSpot::getInstance()->getObjectsField()->findById($fieldId);
    }

    /**
     * @inheritdoc
     */
    protected static function tableAlias(): string
    {
        return ObjectAssociation::tableAlias();
    }

    /**
     * @inheritdoc
     */
    public static function recordClass(): string
    {
        return ObjectAssociation::class;
    }
}
