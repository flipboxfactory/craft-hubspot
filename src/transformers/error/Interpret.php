<?php

/**
 * @copyright  Copyright (c) Flipbox Digital Limited
 * @license    https://flipboxfactory.com/software/hubspot/license
 * @link       https://www.flipboxfactory.com/software/hubspot/
 */

namespace flipbox\hubspot\transformers\error;

use Flipbox\Skeleton\Helpers\ArrayHelper;
use Flipbox\Transform\Traits\MapperTrait;

/**
 * @author Flipbox Factory <hello@flipboxfactory.com>
 * @since 1.0.0
 */
class Interpret
{
    use MapperTrait;

    /**
     * @inheritdoc
     */
    public function __invoke(array $data)
    {
        if (empty($data)) {
            return [
                'error' => 'An unknown error occurred.'
            ];
        }

        return $this->mapFrom(
            $this->normalizeErrors($data)
        );
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
