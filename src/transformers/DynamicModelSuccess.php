<?php

/**
 * @copyright  Copyright (c) Flipbox Digital Limited
 * @license    https://flipboxfactory.com/software/hubspot/license
 * @link       https://www.flipboxfactory.com/software/hubspot/
 */

namespace flipbox\hubspot\transformers;

use yii\base\DynamicModel;

/**
 * @author Flipbox Factory <hello@flipboxfactory.com>
 * @since 1.0.0
 */
class DynamicModelSuccess
{
    /**
     * @param array $data
     * @return mixed
     */
    public function __invoke(array $data)
    {
        return new DynamicModel(array_keys($data), $data);
    }
}
