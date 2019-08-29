<?php

/**
 * @copyright  Copyright (c) Flipbox Digital Limited
 * @license    https://flipboxfactory.com/software/hubspot/license
 * @link       https://www.flipboxfactory.com/software/hubspot/
 */

namespace flipbox\craft\hubspot\events;

use craft\base\ElementInterface;
use yii\base\Event;

/**
 * @param ElementInterface $sender
 */
class PrepVisitorDataFromHubSpotEvent extends Event
{
    const EVENT_NAME = 'PrepareVisitorDataForStorageEvent';

    /**
     * @var array
     */
    public $contact = [];
}
