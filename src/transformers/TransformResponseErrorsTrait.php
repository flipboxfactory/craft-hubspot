<?php

/**
 * @noinspection PhpUnusedParameterInspection
 *
 * @copyright  Copyright (c) Flipbox Digital Limited
 * @license    https://flipboxfactory.com/software/hubspot/license
 * @link       https://www.flipboxfactory.com/software/hubspot/
 */

namespace flipbox\craft\hubspot\transformers;

use Psr\Http\Message\ResponseInterface;
use yii\base\DynamicModel;

/**
 * @author Flipbox Factory <hello@flipboxfactory.com>
 * @since 1.0.0
 */
trait TransformResponseErrorsTrait
{
    /**
     * @param ResponseInterface $response
     * @param array $data
     * @return DynamicModel
     */
    protected function transformResponseErrors(ResponseInterface $response, array $data): DynamicModel
    {
        $errors = call_user_func_array(
            new InterpretResponseErrors(),
            [
                $data
            ]
        );

        $model = new DynamicModel();
        $model->addErrors($errors);

        return $model;
    }
}
