<?php

/**
 * @copyright  Copyright (c) Flipbox Digital Limited
 * @license    https://github.com/flipbox/hubspot/blob/master/LICENSE.md
 * @link       https://github.com/flipbox/hubspot
 */

namespace flipbox\craft\hubspot\transformers;

/**
 * @author Flipbox Factory <hello@flipboxfactory.com>
 * @since 1.0.0
 */
class InterpretUpsertResponseErrors extends InterpretResponseErrors
{
    use UpsertErrorInterpreterTrait;

    /**
     * @param array $errors
     * @return array
     */
    public function normalizeErrors(array $errors): array
    {
        $preparedErrors = parent::normalizeErrors($errors);

        if (!empty($errors['validationResults'])) {
            $preparedErrors = array_merge(
                $preparedErrors,
                $this->prepareValidationErrors($errors['validationResults'])
            );
        }

        return $preparedErrors;
    }
}
