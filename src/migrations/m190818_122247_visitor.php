<?php

/**
 * @copyright  Copyright (c) Flipbox Digital Limited
 * @license    https://flipboxfactory.com/software/hubspot/license
 * @link       https://www.flipboxfactory.com/software/hubspot/
 */

namespace flipbox\craft\hubspot\migrations;

use craft\db\Migration;
use flipbox\craft\hubspot\records\Visitor;

class m190818_122247_visitor extends Migration
{
    /**
     * @inheritdoc
     * @throws \yii\base\NotSupportedException
     * @throws \yii\db\Exception
     */
    public function safeUp()
    {
        $this->createTable(Visitor::tableName(), [
            'id' => $this->primaryKey(),
            'token' => $this->string()->notNull(),
            'contact' => $this->text(),
            'status' => $this->enum(
                'status',
                [
                    Visitor::STATUS_SUCCESSFUL,
                    Visitor::STATUS_PENDING,
                    Visitor::STATUS_PENDING,
                    Visitor::STATUS_ERROR,
                    Visitor::STATUS_NOT_FOUND
                ]
            )
                ->defaultValue(Visitor::STATUS_PENDING)
                ->notNull(),
            'dateCreated' => $this->dateTime()->notNull(),
            'dateUpdated' => $this->dateTime()->notNull(),
            'uid' => $this->uid()
        ]);

        $this->createIndex(
            $this->db->getIndexName(Visitor::tableName(), 'token', true),
            Visitor::tableName(),
            'token',
            true
        );
    }
}
