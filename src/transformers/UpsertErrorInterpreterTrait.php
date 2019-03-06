<?php

/**
 * @copyright  Copyright (c) Flipbox Digital Limited
 * @license    https://flipboxfactory.com/software/hubspot/license
 * @link       https://www.flipboxfactory.com/software/hubspot/
 */

namespace flipbox\craft\hubspot\transformers;

/**
 * @author Flipbox Factory <hello@flipboxfactory.com>
 * @since 1.0.0
 */
trait UpsertErrorInterpreterTrait
{
    /**
     * @param string $errorMessage
     * @param string $errorCode
     * @param array $fields
     * @return array
     */
    protected function interpretError(string $errorMessage, string $errorCode, array $fields = []): array
    {
        $errorKeys = ($fields ?: $errorCode);

        var_dump($errorMessage);
        var_dump($errorCode);
        var_dump($fields);

        exit;

        return [$errorKeys, $errorMessage, $errorCode];
    }
}
