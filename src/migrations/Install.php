<?php

/**
 * @copyright  Copyright (c) Flipbox Digital Limited
 * @license    https://flipboxfactory.com/software/hubspot/license
 * @link       https://www.flipboxfactory.com/software/hubspot/
 */

namespace flipbox\craft\hubspot\migrations;

use craft\db\Migration;

/**
 * @author Flipbox Factory <hello@flipboxfactory.com>
 * @since 1.0.0
 */
class Install extends Migration
{
    /**
     * @inheritdoc
     */
    public function safeUp()
    {
        if (false === (new ObjectAssociations())->safeUp()) {
            return false;
        };

        if (false === (new m190222_101208_connections())->safeUp()) {
            return false;
        };

        return true;
    }

    /**
     * @inheritdoc
     */
    public function safeDown()
    {
        if (false === (new ObjectAssociations())->safeDown()) {
            return false;
        };

        if (false === (new m190222_101208_connections())->safeDown()) {
            return false;
        };

        return true;
    }
}
