<?php

/**
 * @copyright  Copyright (c) Flipbox Digital Limited
 * @license    https://flipboxfactory.com/software/hubspot/license
 * @link       https://www.flipboxfactory.com/software/hubspot/
 */

namespace flipbox\craft\hubspot\queue;

use Craft;
use craft\base\ElementInterface;
use craft\queue\BaseJob;
use flipbox\craft\ember\objects\ElementAttributeTrait;
use flipbox\craft\ember\objects\FieldAttributeTrait;
use flipbox\craft\hubspot\fields\ObjectsFieldInterface;

/**
 * Sync a HubSpot Object to a Craft Element
 */
class SyncElementFromHubSpotJob extends BaseJob implements \Serializable
{
    use FieldAttributeTrait,
        ElementAttributeTrait;

    /**
     * @var string|null
     */
    public $objectId;

    /**
     * @var callable
     */
    public $transformer;

    /**
     * @inheritdoc
     * @return bool
     */
    public function execute($queue): void
    {
        $field = $this->getField();
        $element = $this->getElement();

        if (!$field instanceof ObjectsFieldInterface || !$element instanceof ElementInterface) {
            return false;
        }

        return $field->syncFromHubSpot(
            $element,
            $this->objectId,
            $this->transformer
        );
    }

    /**
     * @inheritdoc
     */
    public function serialize()
    {
        return serialize([
            'fieldId' => $this->getFieldId(),
            'elementId' => $this->getElementId(),
            'objectId' => $this->objectId,
            'transformer' => $this->transformer
        ]);
    }

    /**
     * @inheritdoc
     */
    public function unserialize($serialized)
    {
        Craft::configure(
            $this,
            unserialize($serialized)
        );
    }
}
