<?php

/**
 * @copyright  Copyright (c) Flipbox Digital Limited
 * @license    https://github.com/flipbox/hubspot/blob/master/LICENSE.md
 * @link       https://github.com/flipbox/hubspot
 */

namespace flipbox\craft\hubspot\transformers;

use craft\base\Element;
use craft\base\ElementInterface;
use flipbox\craft\hubspot\events\PopulateElementFromResponseEvent;
use flipbox\craft\hubspot\fields\Objects;
use flipbox\craft\hubspot\fields\ObjectsFieldInterface;
use flipbox\craft\hubspot\HubSpot;
use Psr\Http\Message\ResponseInterface;

/**
 * @author Flipbox Factory <hello@flipboxfactory.com>
 * @since 1.0.0
 */
class PopulateElementFromResponse
{
    /**
     * An action used to assemble a unique event name.
     *
     * @var string
     */
    public $action;

    /**
     * @param ResponseInterface $response
     * @param ElementInterface $element
     * @param ObjectsFieldInterface $field
     * @return ElementInterface
     */
    public function __invoke(
        ResponseInterface $response,
        ElementInterface $element,
        ObjectsFieldInterface $field
    ): ElementInterface {
        $this->populateElementFromResponse($response, $element, $field);
        return $element;
    }

    /**
     * @param ResponseInterface $response
     * @param ElementInterface|Element $element
     * @param ObjectsFieldInterface $field
     * @return ElementInterface
     */
    protected function populateElementFromResponse(
        ResponseInterface $response,
        ElementInterface $element,
        ObjectsFieldInterface $field
    ): ElementInterface {
        /** @var Objects $field */

        $event = new PopulateElementFromResponseEvent([
            'response' => $response,
            'field' => $field
        ]);

        $name = $event::eventName(
            $field->handle,
            $this->action
        );

        HubSpot::info(sprintf(
            "Populate Element: Event '%s', Element '%s'",
            $name,
            $element->id . ' - ' . $element->title
        ), __METHOD__);

        $element->trigger($name, $event);

        return $event->sender ?: $element;
    }
}
