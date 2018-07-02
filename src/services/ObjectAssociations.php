<?php

/**
 * @copyright  Copyright (c) Flipbox Digital Limited
 * @license    https://flipboxfactory.com/software/hubspot/license
 * @link       https://www.flipboxfactory.com/software/hubspot/
 */

namespace flipbox\hubspot\services;

use Craft;
use craft\base\Element;
use craft\base\ElementInterface;
use craft\errors\ElementNotFoundException;
use craft\helpers\Json;
use flipbox\craft\sortable\associations\db\SortableAssociationQueryInterface;
use flipbox\craft\sortable\associations\records\SortableAssociationInterface;
use flipbox\craft\sortable\associations\services\SortableAssociations;
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
     * @param ElementInterface $element
     * @param Objects $field
     * @return string|null
     */
    public function findObjectIdByElement(ElementInterface $element, Objects $field)
    {
        /** @var Element $element */
        return $this->findObjectId($field->id, $element->getId(), $element->siteId);
    }

    /**
     * @noinspection PhpDocMissingThrowsInspection
     *
     * @param string $fieldId
     * @param string $elementId
     * @param string|null $siteId
     * @return null|string
     */
    public function findObjectId(string $fieldId, string $elementId, string $siteId = null)
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
     * @noinspection PhpDocMissingThrowsInspection
     *
     * @param string $fieldId
     * @param string $elementId
     * @param string|null $siteId
     * @return null|string
     */
    public function findElementId(string $fieldId, string $elementId, string $siteId = null)
    {
        $elementId = HubSpot::getInstance()->getObjectAssociations()->getQuery([
            'select' => ['elementId'],
            'objectId' => $elementId,
            'siteId' => SiteHelper::ensureSiteId($siteId),
            'fieldId' => $fieldId
        ])->scalar();

        return is_string($elementId) ? $elementId : null;
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
     * @param string $objectId
     * @return ElementInterface
     * @throws ElementNotFoundException
     */
    public function getElementByObjectId(string $objectId): ElementInterface
    {
        if (!$element = $this->findElementByObjectId($objectId)) {
            throw new ElementNotFoundException(sprintf(
                "Unable to get element from HubSpot Id: '%s'.",
                $objectId
            ));
        }

        return $element;
    }

    /**
     * @param string $objectId
     * @return ElementInterface|null
     */
    public function findElementByObjectId(string $objectId)
    {
        $record = $this->findByCondition([
            'objectId' => $objectId
        ]);

        if ($record === null) {
            return null;
        }

        return $record->getElement();
    }

    /**
     * Find the HubSpot Id by Element Id
     *
     * @param int $id
     * @return string|null
     */
    public function findObjectIdByElementId(int $id)
    {
        $objectId = $this->getQuery()
            ->select(['objectId'])
            ->element($id)
            ->scalar();

        if (!$objectId) {
            return null;
        }

        return $objectId;
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
