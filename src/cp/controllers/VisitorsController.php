<?php

/**
 * @copyright  Copyright (c) Flipbox Digital Limited
 * @license    https://flipboxfactory.com/software/hubspot/license
 * @link       https://www.flipboxfactory.com/software/hubspot/
 */

namespace flipbox\craft\hubspot\cp\controllers;

use Craft;
use craft\helpers\ArrayHelper;
use flipbox\craft\hubspot\actions\visitors\Delete;
use flipbox\craft\hubspot\actions\visitors\Update;
use flipbox\craft\hubspot\HubSpot;

/**
 * @author Flipbox Factory <hello@flipboxfactory.com>
 * @since 1.1.0
 */
class VisitorsController extends AbstractController
{
    /**
     * @return array
     */
    public function behaviors()
    {
        return ArrayHelper::merge(
            parent::behaviors(),
            [
                'error' => [
                    'default' => 'visitor'
                ],
                'redirect' => [
                    'only' => ['update', 'delete'],
                    'actions' => [
                        'update' => [200],
                        'delete' => [204]
                    ]
                ],
                'flash' => [
                    'actions' => [
                        'update' => [
                            200 => HubSpot::t("HubSpot Visitor updated successfully"),
                            400 => HubSpot::t("Failed to updated HubSpot Visitor")
                        ],
                        'delete' => [
                            204 => HubSpot::t("HubSpot Visitor deleted successfully"),
                            400 => HubSpot::t("Failed to delete HubSpot Visitor")
                        ]
                    ]
                ]
            ]
        );
    }

    /**
     * @param string|null $identifier
     * @return mixed
     * @throws \yii\base\InvalidConfigException
     */
    public function actionUpdate(
        string $identifier = null
    ) {
        if ($identifier === null) {
            $identifier = Craft::$app->getRequest()->getParam(
                'identifier',
                Craft::$app->getRequest()->getParam('id')
            );
        }

        /** @var Update $action */
        return (Craft::createObject([
            'class' => Update::class
        ], [
            'update',
            $this
        ]))->runWithParams([
            'identifier' => $identifier
        ]);
    }

    /**
     * @param string|null $identifier
     * @return mixed
     * @throws \yii\base\InvalidConfigException
     */
    public function actionDelete(
        string $identifier = null
    ) {
        if ($identifier === null) {
            $identifier = Craft::$app->getRequest()->getParam(
                'identifier',
                Craft::$app->getRequest()->getParam('id')
            );
        }

        /** @var Delete $action */
        return (Craft::createObject([
            'class' => Delete::class
        ], [
            'delete',
            $this
        ]))->runWithParams([
            'identifier' => $identifier
        ]);
    }

}