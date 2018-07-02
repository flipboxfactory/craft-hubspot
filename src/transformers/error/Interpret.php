<?php

/**
 * @copyright  Copyright (c) Flipbox Digital Limited
 * @license    https://flipboxfactory.com/software/hubspot/license
 * @link       https://www.flipboxfactory.com/software/hubspot/
 */

namespace flipbox\hubspot\transformers\error;

use Flipbox\Skeleton\Helpers\ArrayHelper;
use Flipbox\Transform\Scope;
use Flipbox\Transform\Traits\MapperTrait;
use Flipbox\Transform\Transformers\AbstractTransformer;

/**
 * @author Flipbox Factory <hello@flipboxfactory.com>
 * @since 1.0.0
 */
class Interpret extends AbstractTransformer
{
    use MapperTrait;

    /**
     * @inheritdoc
     */
    public function __invoke($data, Scope $scope, string $identifier = null, $source = null)
    {
        if (empty($data)) {
            return [
                'error' => 'An unknown error occurred.'
            ];
        }

        return $this->transform(
            $this->mapFrom(
                $this->normalizeErrors($data)
            ),
            $source
        );
    }

    /**
     * @param array $errors
     * @param $source
     * @return array
     */
    public function transform(array $errors, $source = null)
    {
        return $errors;
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
