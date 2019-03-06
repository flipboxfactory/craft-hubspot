<?php

/**
 * @copyright  Copyright (c) Flipbox Digital Limited
 * @license    https://github.com/flipbox/hubspot/blob/master/LICENSE.md
 * @link       https://github.com/flipbox/hubspot
 */

namespace flipbox\craft\hubspot\transformers;

use craft\helpers\ArrayHelper;

/**
 * @author Flipbox Factory <hello@flipboxfactory.com>
 * @since 1.0.0
 */
class InterpretResponseErrors
{
    /**
     * @inheritdoc
     */
    public function __invoke(array $data): array
    {
        if (empty($data)) {
            return [
                'error' => 'An unknown error occurred.'
            ];
        }

        return $this->normalizeErrors($data);
    }

    /**
     * @param array $errors
     * @return array
     */
    public function normalizeErrors(array $errors): array
    {
        $preparedErrors = [];

        $status = $errors['status'] ?? null;

        if (in_array($status, ['error', 'exception'], true)) {
            list($errorKey, $errorMessage) = $this->prepareError($errors);
            $preparedErrors[$errorKey] = $errorMessage;
        }

        return $preparedErrors;
    }

    /**
     * @param array $error
     * @return array
     */
    protected function prepareError(array $error): array
    {
        return [
            ArrayHelper::getValue($error, 'status'),
            ArrayHelper::getValue($error, 'message')
        ];
    }
}
