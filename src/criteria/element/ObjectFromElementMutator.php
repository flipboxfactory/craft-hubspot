<?php

/**
 * @copyright  Copyright (c) Flipbox Digital Limited
 * @license    https://flipboxfactory.com/software/hubspot/license
 * @link       https://www.flipboxfactory.com/software/hubspot/
 */

namespace flipbox\hubspot\criteria\element;

use craft\errors\ElementNotFoundException;
use craft\errors\FieldNotFoundException;
use flipbox\hubspot\criteria\ObjectMutatorInterface;
use flipbox\hubspot\traits\TransformElementPayloadTrait;

/**
 * @author Flipbox Factory <hello@flipboxfactory.com>
 * @since 1.0.0
 */
class ObjectFromElementMutator extends ObjectFromElementAccessor implements ObjectMutatorInterface
{
    use TransformElementPayloadTrait;

    /**
     * @inheritdoc
     * @throws FieldNotFoundException
     * @throws ElementNotFoundException
     */
    public function getPayload(): array
    {
        return $this->transformElementPayload(
            $this->getElement(),
            $this->getField()
        );
    }
}
