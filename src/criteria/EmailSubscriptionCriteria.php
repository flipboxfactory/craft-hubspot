<?php

/**
 * @copyright  Copyright (c) Flipbox Digital Limited
 * @license    https://flipboxfactory.com/software/hubspot/license
 * @link       https://www.flipboxfactory.com/software/hubspot/
 */

namespace flipbox\craft\hubspot\criteria;

use craft\elements\User;
use flipbox\craft\ember\objects\ElementAttributeTrait;

/**
 * @author Flipbox Factory <hello@flipboxfactory.com>
 * @since 1.2.0
 */
class EmailSubscriptionCriteria extends \Flipbox\HubSpot\Criteria\EmailSubscriptionCriteria
{
    use CacheTrait,
        ConnectionTrait,
        ElementAttributeTrait;

    /**
     * @return string|null
     */
    public function findId()
    {
        if (null === $this->id) {
            $this->id = $this->resolveId();
        }

        return $this->id;
    }

    /**
     * @return string|null
     */
    protected function resolveId()
    {
        $element = $this->getElement();

        if (!$element instanceof User) {
            return null;
        }

        return $element->email;
    }
}
