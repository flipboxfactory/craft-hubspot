<?php

/**
 * @copyright  Copyright (c) Flipbox Digital Limited
 * @license    https://flipboxfactory.com/software/hubspot/license
 * @link       https://www.flipboxfactory.com/software/hubspot/
 */

namespace flipbox\craft\hubspot\transformers;

use flipbox\craft\ember\helpers\ArrayHelper;

/**
 * @author Flipbox Factory <hello@flipboxfactory.com>
 * @since 1.0.0
 */
trait UpsertErrorInterpreterTrait
{
    /**
     * @param array $errors
     * @return array
     */
    protected function prepareValidationErrors(array $errors): array
    {
        $validationErrors = [];

        foreach ($errors as $error) {
            if ($property = ArrayHelper::getValue($error, 'name')) {
                $validationErrors[$property] = ArrayHelper::getValue($error, 'message');
            }
        }

        return $validationErrors;
    }
}
