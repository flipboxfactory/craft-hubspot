<?php

/**
 * @copyright  Copyright (c) Flipbox Digital Limited
 * @license    https://flipboxfactory.com/software/hubspot/license
 * @link       https://www.flipboxfactory.com/software/hubspot/
 */

namespace flipbox\hubspot\events;

use craft\base\ElementInterface;
use flipbox\hubspot\fields\actions\ObjectActionInterface;
use flipbox\hubspot\fields\actions\ObjectItemActionInterface;
use yii\base\Event;

/**
 * @author Flipbox Factory <hello@flipboxfactory.com>
 * @since 1.0.0
 */
class RegisterObjectFieldActionsEvent extends Event
{
    /**
     * @var ObjectActionInterface[]|ObjectItemActionInterface[]
     */
    public $actions = [];

    /**
     * @var ElementInterface
     */
    public $element;
}
