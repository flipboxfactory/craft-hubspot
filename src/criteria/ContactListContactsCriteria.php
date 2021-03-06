<?php

/**
 * @copyright  Copyright (c) Flipbox Digital Limited
 * @license    https://flipboxfactory.com/software/hubspot/license
 * @link       https://www.flipboxfactory.com/software/hubspot/
 */

namespace flipbox\craft\hubspot\criteria;

use flipbox\craft\ember\objects\ElementAttributeTrait;
use flipbox\craft\ember\objects\FieldAttributeTrait;
use flipbox\craft\ember\objects\SiteAttributeTrait;

/**
 * @author Flipbox Factory <hello@flipboxfactory.com>
 * @since 1.0.0
 */
class ContactListContactsCriteria extends \Flipbox\HubSpot\Criteria\ContactListContactsCriteria
{
    use CacheTrait,
        ConnectionTrait,
        IdAttributeFromElementTrait,
        ElementAttributeTrait,
        FieldAttributeTrait,
        SiteAttributeTrait;
}
