<?php

/**
 * @copyright  Copyright (c) Flipbox Digital Limited
 * @license    https://flipboxfactory.com/software/hubspot/license
 * @link       https://www.flipboxfactory.com/software/hubspot/
 */

namespace flipbox\craft\hubspot\helpers;

use Craft;

/**
 * @author Flipbox Factory <hello@flipboxfactory.com>
 * @since 1.0.0
 */
class TransformerHelper
{
    /**
     * The sync payload action name
     */
    const PAYLOAD_ACTION_SYNC = 'sync';

    /**
     * The sync payload action name
     */
    const PAYLOAD_ACTION_SAVE = 'save';

    /**
     * @param $transformer
     * @return bool
     */
    public static function isTransformerClass($transformer): bool
    {
        return is_string($transformer) &&
            class_exists($transformer) &&
            (
                method_exists($transformer, '__invoke') ||
                is_callable([$transformer, '__invoke'])
            );
    }

    /**
     * @param $transformer
     * @return bool
     */
    public static function isTransformerClassArray($transformer): bool
    {
        if (!is_array($transformer)) {
            false;
        }

        return static::isTransformerClass($transformer['class'] ?? null);
    }

    /**
     * @noinspection PhpDocMissingThrowsInspection
     *
     * @param $transformer
     * @return callable|null
     */
    public static function resolveTransformer($transformer)
    {
        if (is_callable($transformer)) {
            return $transformer;
        }

        if (static::isTransformerClass($transformer)) {
            return new $transformer();
        }

        if (static::isTransformerClassArray($transformer)) {
            /** @noinspection PhpIncompatibleReturnTypeInspection */
            /** @noinspection PhpUnhandledExceptionInspection */
            return Craft::createObject($transformer);
        }

        return null;
    }
}
