<?php

namespace flipbox\hubspot\migrations;

use craft\db\Migration;
use craft\records\Element as ElementRecord;

class ObjectTable extends Migration
{
    /**
     * @var array
     */
    public $tableAlias;

    /**
     * @return string
     */
    private function getTableName(): string
    {
        return '{{%' . $this->tableAlias . '}}';
    }

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
     * Creates the tables.
     *
     * @return void
     */
    protected function createTables()
    {
        $this->dropTableIfExists($this->getTableName());
        $this->createTable(
            $this->getTableName(),
            [
                'id' => $this->primaryKey(),
                'elementId' => $this->integer(),
                'hubspotId' => $this->char(48),
                'dateCreated' => $this->dateTime()->notNull(),
                'dateUpdated' => $this->dateTime()->notNull(),
                'uid' => $this->uid()
            ]
        );
    }

    /**
     * Creates the indexes.
     *
     * @return void
     */
    protected function createIndexes()
    {
        $this->createIndex(
            $this->db->getIndexName(
                $this->getTableName(),
                'elementId',
                true,
                true
            ),
            $this->getTableName(),
            'elementId',
            true
        );
        $this->createIndex(
            $this->db->getIndexName(
                $this->getTableName(),
                'hubspotId',
                false,
                false
            ),
            $this->getTableName(),
            'hubspotId',
            false
        );
        $this->createIndex(
            $this->db->getIndexName(
                $this->getTableName(),
                'elementId,hubspotId',
                true
            ),
            $this->getTableName(),
            'elementId,hubspotId',
            true
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
            $this->db->getForeignKeyName(
                $this->getTableName(),
                'elementId'
            ),
            $this->getTableName(),
            'elementId',
            ElementRecord::tableName(),
            'id',
            'CASCADE',
            null
        );
    }

    /**
     * @inheritdoc
     */
    public function safeDown()
    {
        $this->dropTableIfExists($this->getTableName());
        return true;
    }
}
