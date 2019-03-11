<?php

/**
 * @copyright  Copyright (c) Flipbox Digital Limited
 * @license    https://flipboxfactory.com/software/hubspot/license
 * @link       https://www.flipboxfactory.com/software/hubspot/
 */

namespace flipbox\craft\hubspot\actions\connections;

use flipbox\craft\hubspot\records\Connection;

/**
 * @author Flipbox Factory <hello@flipboxfactory.com>
 * @since 1.0.0
 */
trait LookupConnectionTrait
{
    /**
     * @inheritdoc
     * @return Connection
     */
    protected function find($identifier)
    {
        return Connection::findOne($identifier);
    }
}
