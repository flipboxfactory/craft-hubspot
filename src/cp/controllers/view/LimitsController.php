<?php

/**
 * @copyright  Copyright (c) Flipbox Digital Limited
 * @license    https://flipboxfactory.com/software/hubspot/license
 * @link       https://www.flipboxfactory.com/software/hubspot/
 */

namespace flipbox\craft\hubspot\cp\controllers\view;

use flipbox\craft\hubspot\criteria\HubCriteria;
use flipbox\craft\hubspot\transformers\DynamicModelResponse;
use yii\web\Response;

/**
 * @author Flipbox Factory <hello@flipboxfactory.com>
 * @since 1.0.0
 */
class LimitsController extends AbstractController
{
    /**
     * The template base path
     */
    const TEMPLATE_BASE = parent::TEMPLATE_BASE . '/limits';

    /**
     * The index view template path
     */
    const TEMPLATE_INDEX = self::TEMPLATE_BASE . '/index';

    /**
     * @return Response
     */
    public function actionIndex(): Response
    {
        $variables = [];
        $this->baseVariables($variables);

        if (null !== ($connection = $this->findActiveConnection())) {
            $criteria = new HubCriteria([
                'connection' => $connection->getConnection()
            ]);

            $model = call_user_func_array(
                new DynamicModelResponse(),
                [
                    $criteria->dailyLimit()
                ]
            );
        }

        $variables['limits'] = $model ?? $this->invalidConnectionModel();

        return $this->renderTemplate(
            static::TEMPLATE_INDEX,
            $variables
        );
    }

    /*******************************************
     * BASE PATHS
     *******************************************/

    /**
     * @return string
     */
    protected function getBaseCpPath(): string
    {
        return parent::getBaseCpPath() . '/limits';
    }

    /**
     * @return string
     */
    protected function getBaseActionPath(): string
    {
        return parent::getBaseActionPath() . '/limits';
    }
}
