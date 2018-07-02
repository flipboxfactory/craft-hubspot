<?php

/**
 * @copyright  Copyright (c) Flipbox Digital Limited
 * @license    https://flipboxfactory.com/software/hubspot/license
 * @link       https://www.flipboxfactory.com/software/hubspot/
 */

namespace flipbox\hubspot\migrations;

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
        (new ObjectAssociation())
            ->safeUp();

        return true;
    }

    /**
     * @inheritdoc
     */
    public function safeDown()
    {
        (new ObjectAssociation())
            ->safeDown();

        return true;
    }
}
