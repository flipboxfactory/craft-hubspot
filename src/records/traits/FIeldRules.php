<?php

/**
 * @copyright  Copyright (c) Flipbox Digital Limited
 * @license    https://flipboxfactory.com/software/hubspot/license
 * @link       https://www.flipboxfactory.com/software/hubspot/
 */

namespace flipbox\hubspot\records\traits;

use craft\base\Field;
use craft\base\FieldInterface;
use flipbox\ember\helpers\ModelHelper;

/**
 * @property int|null $fieldId
 * @property Field|FieldInterface|null $field
 *
 * @author Flipbox Factory <hello@flipboxfactory.com>
 * @since 1.0.0
 */
trait FieldRules
{
    /**
     * @return array
     */
    protected function fieldRules(): array
    {
        return [
            [
                [
                    'fieldId'
                ],
                'number',
                'integerOnly' => true
            ],
            [
                [
                    'fieldId',
                    'field'
                ],
                'safe',
                'on' => [
                    ModelHelper::SCENARIO_DEFAULT
                ]
            ]
        ];
    }
}
