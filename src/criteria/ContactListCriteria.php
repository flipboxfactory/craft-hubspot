<?php

/**
 * @copyright  Copyright (c) Flipbox Digital Limited
 * @license    https://flipboxfactory.com/software/hubspot/license
 * @link       https://www.flipboxfactory.com/software/hubspot/
 */

namespace flipbox\craft\hubspot\criteria;

/**
 * @author Flipbox Factory <hello@flipboxfactory.com>
 * @since 1.0.0
 */
class ContactListCriteria extends \Flipbox\HubSpot\Criteria\ContactListCriteria
{
    use CacheTrait,
        ConnectionTrait,
        ElementTrait;
}
