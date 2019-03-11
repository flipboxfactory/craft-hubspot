<?php

/**
 * @copyright  Copyright (c) Flipbox Digital Limited
 * @license    https://flipboxfactory.com/software/hubspot/license
 * @link       https://www.flipboxfactory.com/software/hubspot/
 */

namespace flipbox\craft\hubspot\migrations;

use flipbox\craft\hubspot\records\Connection as ConnectionRecord;
use flipbox\craft\integration\migrations\IntegrationConnections;

class m190222_101208_connections extends IntegrationConnections
{
    /**
     * @return string
     */
    protected static function tableName(): string
    {
        return ConnectionRecord::tableName();
    }
}
