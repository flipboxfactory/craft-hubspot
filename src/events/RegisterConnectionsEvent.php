<?php

/**
 * @copyright  Copyright (c) Flipbox Digital Limited
 * @license    https://flipboxfactory.com/software/hubspot/license
 * @link       https://www.flipboxfactory.com/software/hubspot/
 */

namespace flipbox\hubspot\events;

use flipbox\hubspot\connections\ConnectionInterface;
use yii\base\Event;

/**
 * @author Flipbox Factory <hello@flipboxfactory.com>
 * @since 1.0.0
 */
class RegisterConnectionsEvent extends Event
{
    /**
     * @var array|ConnectionInterface[]
     */
    public $connections = [];
}
