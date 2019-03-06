<?php

/**
 * @noinspection PhpUnusedParameterInspection
 *
 * @copyright  Copyright (c) Flipbox Digital Limited
 * @license    https://github.com/flipbox/hubspot/blob/master/LICENSE.md
 * @link       https://github.com/flipbox/hubspot
 */

namespace flipbox\craft\hubspot\transformers;

use craft\base\Element;
use craft\base\ElementInterface;
use flipbox\craft\hubspot\events\CreatePayloadFromElementEvent;
use flipbox\craft\hubspot\fields\Objects;
use flipbox\craft\hubspot\fields\ObjectsFieldInterface;
use flipbox\craft\hubspot\HubSpot;
use yii\base\BaseObject;

/**
 * @author Flipbox Factory <hello@flipboxfactory.com>
 * @since 1.0.0
 */
class CreateUpsertPayloadFromElement extends BaseObject
{
    /**
     * An action used to assemble a unique event name.
     *
     * @var string
     */
    public $action;

    /**
     * @param ElementInterface|Element $element
     * @param ObjectsFieldInterface $field
     * @return array
     */
    public function __invoke(
        ElementInterface $element,
        ObjectsFieldInterface $field
    ): array {
        /** @var Objects $field */

        $event = new CreatePayloadFromElementEvent([
            'payload' => $this->createPayload($element, $field)
        ]);

        $name = $event::eventName(
            $field->handle,
            $this->action
        );

        HubSpot::info(sprintf(
            "Create payload: Event '%s', Element '%s'",
            $name,
            $element->id . ' - ' . $element->title
        ), __METHOD__);

        $element->trigger($name, $event);

        return $event->getPayload();
    }

    /**
     * @param ElementInterface $element
     * @param ObjectsFieldInterface $field
     * @return array
     *
     * @noinspection PhpUnusedParameterInspection
     */
    public function createPayload(
        ElementInterface $element,
        ObjectsFieldInterface $field
    ): array {
        /** @var Element $element */

        return [];
    }
}
