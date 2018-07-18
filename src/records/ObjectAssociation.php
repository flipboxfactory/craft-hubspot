<?php

/**
 * @copyright  Copyright (c) Flipbox Digital Limited
 * @license    https://flipboxfactory.com/software/hubspot/license
 * @link       https://www.flipboxfactory.com/software/hubspot/
 */

namespace flipbox\hubspot\records;

use Craft;
use flipbox\craft\integration\records\IntegrationAssociation;
use flipbox\craft\sortable\associations\services\SortableAssociations;
use flipbox\hubspot\db\ObjectAssociationQuery;
use flipbox\hubspot\fields\Objects;
use flipbox\hubspot\HubSpot;
use flipbox\hubspot\services\ObjectAssociations;

/**
 * @author Flipbox Factory <hello@flipboxfactory.com>
 * @since 1.0.0
 *
 * @property int $fieldId
 * @property string $objectId
 */
class ObjectAssociation extends IntegrationAssociation
{
    /**
     * The table alias
     */
    const TABLE_ALIAS = 'hubspot_objects';

    /**
     * @inheritdoc
     * @throws \Throwable
     */
    public function __construct($config = [])
    {
        HubSpot::getInstance()->getObjectAssociations()->ensureTableExists();
        parent::__construct($config);
    }

    /**
     * {@inheritdoc}
     */
    public static function tableAlias()
    {
        return parent::tableAlias() . HubSpot::getInstance()->getSettings()->environmentTablePostfix;
    }

    /**
     * @inheritdoc
     * @return ObjectAssociations
     */
    protected function associationService(): SortableAssociations
    {
        return HubSpot::getInstance()->getObjectAssociations();
    }

    /**
     * @noinspection PhpDocMissingThrowsInspection
     * @return ObjectAssociationQuery
     */
    public static function find(): ObjectAssociationQuery
    {
        /** @noinspection PhpIncompatibleReturnTypeInspection */
        /** @noinspection PhpUnhandledExceptionInspection */
        return Craft::createObject(ObjectAssociationQuery::class, [get_called_class()]);
    }

    /**
     * @param array $criteria
     * @return mixed|null
     * @throws \yii\base\InvalidConfigException
     */
    public function getObject(array $criteria = [])
    {
        if (null === ($field = $this->getField())) {
            return null;
        }

        if (!$field instanceof Objects) {
            return null;
        }

        $base = [
            'connection' => $field->getConnection(),
            'cache' => $field->getCache()
        ];

        $resource = $field->getResource();

        // Can't override these...
        $criteria['id'] = $this->{self::TARGET_ATTRIBUTE} ?: self::DEFAULT_ID;
        $criteria['object'] = $field->object;

        return $resource->read(
            $resource->getAccessorCriteria(
                array_merge(
                    $base,
                    $criteria
                )
            )
        );
    }
}
