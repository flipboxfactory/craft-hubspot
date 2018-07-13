<?php

/**
 * @copyright  Copyright (c) Flipbox Digital Limited
 * @license    https://flipboxfactory.com/software/hubspot/license
 * @link       https://www.flipboxfactory.com/software/hubspot/
 */

namespace flipbox\hubspot\traits;

use craft\base\ElementInterface;
use flipbox\hubspot\fields\Objects;
use flipbox\hubspot\transformers\elements\ObjectId;
use Flipbox\Transform\Factory;

/**
 * @author Flipbox Factory <hello@flipboxfactory.com>
 * @since 1.0.0
 */
trait TransformElementIdTrait
{
    /**
     * @param ElementInterface $element
     * @param Objects $field
     * @return null|string
     */
    protected function transformElementId(
        ElementInterface $element,
        Objects $field
    ) {
        /** @noinspection PhpIncompatibleReturnTypeInspection */
        return Factory::item(
            new ObjectId($field),
            $element
        );
    }
}
