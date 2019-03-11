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
 * @since 2.0.0
 */
class ContactCriteria extends \Flipbox\HubSpot\Criteria\ContactCriteria
{
    use CacheTrait,
        ConnectionTrait,
        IdAttributeFromElementTrait,
        PayloadAttributeFromElementTrait,
        ElementAttributeTrait,
        FieldAttributeTrait,
        SiteAttributeTrait;

    public function getPayload(): array
    {
        return [
            'properties' => [
                ['property' => 'email', 'value' => 'asdfsadfsafd@asdfadsffads.com'],
            ]
        ];
    }
}