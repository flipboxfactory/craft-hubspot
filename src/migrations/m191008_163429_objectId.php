<?php

/**
 * @copyright  Copyright (c) Flipbox Digital Limited
 * @license    https://flipboxfactory.com/software/hubspot/license
 * @link       https://www.flipboxfactory.com/software/hubspot/
 */

namespace flipbox\craft\hubspot\migrations;

use craft\db\Migration;
use flipbox\craft\hubspot\records\ObjectAssociation;
use yii\db\Schema;

class m191008_163429_objectId extends Migration
{
    /**
     * @inheritdoc
     * @throws \yii\base\NotSupportedException
     * @throws \yii\db\Exception
     */
    public function safeUp()
    {
        https://stackoverflow.com/questions/10255724/incorrect-integer-2147483647-is-inserted-into-mysql
        $this->alterColumn(
            ObjectAssociation::tableName(),
            'objectId',
            Schema::TYPE_BIGINT
        );
    }
}
