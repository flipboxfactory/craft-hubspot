<?php

/**
 * @copyright  Copyright (c) Flipbox Digital Limited
 * @license    https://flipboxfactory.com/software/hubspot/license
 * @link       https://www.flipboxfactory.com/software/hubspot/
 */

namespace flipbox\hubspot\criteria\element;

use Craft;
use craft\base\ElementInterface;
use craft\errors\ElementNotFoundException;

/**
 * @author Flipbox Factory <hello@flipboxfactory.com>
 * @since 1.0.0
 */
class ObjectFromUserMutator extends ObjectFromElementMutator
{
    /**
     * @return ElementInterface
     * @throws ElementNotFoundException
     */
    protected function getElement(): ElementInterface
    {
        if ($this->element instanceof ElementInterface) {
            return $this->element;
        }

        if (is_numeric($this->element)) {
            return $this->element = Craft::$app->getUsers()->getUserById($this->element);
        }

        if (is_string($this->element)) {
            return $this->element = Craft::$app->getUsers()->getUserByUsernameOrEmail($this->element);
        }

        throw new ElementNotFoundException("Unable to resolve element.");
    }
}
