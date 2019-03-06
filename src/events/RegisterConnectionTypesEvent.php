<?php

/**
 * @copyright  Copyright (c) Flipbox Digital Limited
 * @license    https://flipboxfactory.com/software/hubspot/license
 * @link       https://www.flipboxfactory.com/software/hubspot/
 */

namespace flipbox\craft\hubspot\events;

use flipbox\craft\hubspot\records\Connection;
use yii\base\Event;

/**
 * @author Flipbox Factory <hello@flipboxfactory.com>
 * @since 1.0.0
 */
class RegisterConnectionTypesEvent extends Event
{
    /**
     * Event to register connections
     */
    const REGISTER_CONNECTIONS = 'registerConnectionTypes';

    /**
     * @var array|Connection[]
     */
    public $types = [];
}
