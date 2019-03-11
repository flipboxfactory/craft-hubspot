<?php

/**
 * @copyright  Copyright (c) Flipbox Digital Limited
 * @license    https://flipboxfactory.com/software/hubspot/license
 * @link       https://www.flipboxfactory.com/software/hubspot/
 */

namespace flipbox\craft\hubspot\fields;

use craft\base\ElementInterface;
use craft\base\FieldInterface;
use Psr\Http\Message\ResponseInterface;

/**
 * @author Flipbox Factory <hello@flipboxfactory.com>
 * @since 1.0.0
 */
interface ObjectsFieldInterface extends FieldInterface
{
    /**
     * @return string
     */
    public function getObjectLabel(): string;

    /**
     * @param string $id
     * @return ResponseInterface
     */
    public function readFromHubSpot(
        string $id
    ): ResponseInterface;

    /**
     * @param ElementInterface $element
     * @param string|null $objectId
     * @param callable|array|string $transformer
     * @return bool
     */
    public function syncFromHubSpot(
        ElementInterface $element,
        string $objectId = null,
        $transformer = null
    ): bool;

    /**
     * @param ElementInterface $element
     * @param string|null $objectId
     * @param callable|array|string $transformer
     * @return bool
     */
    public function syncToHubSpot(
        ElementInterface $element,
        string $objectId = null,
        $transformer = null
    ): bool;
}
