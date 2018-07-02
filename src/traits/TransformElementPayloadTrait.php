<?php

/**
 * @copyright  Copyright (c) Flipbox Digital Limited
 * @license    https://flipboxfactory.com/software/hubspot/license
 * @link       https://www.flipboxfactory.com/software/hubspot/
 */

namespace flipbox\hubspot\traits;

use craft\base\ElementInterface;
use flipbox\flux\helpers\TransformerHelper;
use flipbox\hubspot\fields\Objects;
use flipbox\hubspot\HubSpot;
use Flipbox\Transform\Factory;

/**
 * @author Flipbox Factory <hello@flipboxfactory.com>
 * @since 1.0.0
 */
trait TransformElementPayloadTrait
{
    /**
     * @param ElementInterface $element
     * @param Objects $field
     * @return array
     */
    protected function transformElementPayload(
        ElementInterface $element,
        Objects $field
    ): array {

        $transformer = HubSpot::getInstance()->getTransformers()->find(
            TransformerHelper::eventName([$field->object, 'payload']),
            get_class($element)
        );

        if ($transformer !== null) {
            return (array)Factory::item(
                $transformer,
                $element
            );
        }

        return [];
    }
}
