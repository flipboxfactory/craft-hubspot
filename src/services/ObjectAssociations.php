<?php

/**
 * @copyright  Copyright (c) Flipbox Digital Limited
 * @license    https://flipboxfactory.com/software/hubspot/license
 * @link       https://www.flipboxfactory.com/software/hubspot/
 */

namespace flipbox\hubspot\services;

use Craft;
use flipbox\craft\integration\services\IntegrationAssociations;
use flipbox\craft\integration\services\IntegrationField;
use flipbox\hubspot\db\ObjectAssociationQuery;
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
class ObjectAssociations extends IntegrationAssociations
{
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
     * @inheritdoc
     * @return ObjectsField
     */
    protected function fieldService(): IntegrationField
    {
        return HubSpot::getInstance()->getObjectsField();
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
}
