<?php

/**
 * @copyright  Copyright (c) Flipbox Digital Limited
 * @license    https://flipboxfactory.com/software/hubspot/license
 * @link       https://www.flipboxfactory.com/software/hubspot/
 */

namespace flipbox\hubspot\migrations;

use craft\db\Migration;
use craft\records\Element as ElementRecord;
use craft\records\Field as FieldRecord;
use craft\records\Site as SiteRecord;
use flipbox\hubspot\records\ObjectAssociation as ObjectAssociationRecord;

/**
 * @author Flipbox Factory <hello@flipboxfactory.com>
 * @since 1.0.0
 */
class ObjectAssociation extends Migration
{
    /**
     * @inheritdoc
     */
    public function safeUp()
    {
        $this->createTables();
        $this->createIndexes();
        $this->addForeignKeys();

        return true;
    }

    /**
     * @inheritdoc
     */
    public function safeDown()
    {
        $this->dropTableIfExists(ObjectAssociationRecord::tableName());
        return true;
    }

    /**
     * Creates the tables.
     *
     * @return void
     */
    protected function createTables()
    {
        $this->createTable(ObjectAssociationRecord::tableName(), [
            'objectId' => $this->integer()->notNull(),
            'elementId' => $this->integer()->notNull(),
            'fieldId' => $this->integer()->notNull(),
            'siteId' => $this->integer()->notNull(),
            'sortOrder' => $this->smallInteger()->unsigned(),
            'dateCreated' => $this->dateTime()->notNull(),
            'dateUpdated' => $this->dateTime()->notNull(),
            'uid' => $this->uid()
        ]);
    }

    /**
     * Creates the indexes.
     *
     * @return void
     */
    protected function createIndexes()
    {
        $this->addPrimaryKey(
            null,
            ObjectAssociationRecord::tableName(),
            [
                'elementId',
                'objectId',
                'fieldId',
                'siteId'
            ]
        );
        $this->createIndex(
            null,
            ObjectAssociationRecord::tableName(),
            'objectId',
            false
        );
    }

    /**
     * Adds the foreign keys.
     *
     * @return void
     */
    protected function addForeignKeys()
    {
        $this->addForeignKey(
            null,
            ObjectAssociationRecord::tableName(),
            'elementId',
            ElementRecord::tableName(),
            'id',
            'CASCADE',
            null
        );
        $this->addForeignKey(
            null,
            ObjectAssociationRecord::tableName(),
            'siteId',
            SiteRecord::tableName(),
            'id',
            'CASCADE',
            'CASCADE'
        );
        $this->addForeignKey(
            null,
            ObjectAssociationRecord::tableName(),
            'fieldId',
            FieldRecord::tableName(),
            'id',
            'CASCADE',
            'CASCADE'
        );
    }
}
