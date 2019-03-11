<?php

/**
 * @copyright  Copyright (c) Flipbox Digital Limited
 * @license    https://flipboxfactory.com/software/hubspot/license
 * @link       https://www.flipboxfactory.com/software/hubspot/
 */

namespace flipbox\craft\hubspot\cp\controllers;

use Craft;
use craft\base\ElementInterface;
use craft\helpers\ArrayHelper;
use flipbox\craft\hubspot\cp\actions\widgets\SyncFrom;
use flipbox\craft\hubspot\HubSpot;
use yii\web\HttpException;

/**
 * @author Flipbox Factory <hello@flipboxfactory.com>
 * @since 1.0.0
 */
class WidgetsController extends AbstractController
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
                    'default' => 'criteria'
                ],
                'redirect' => [
                    'only' => ['sync-from'],
                    'actions' => [
                        'sync-from' => [200]
                    ]
                ],
                'flash' => [
                    'actions' => [
                        'sync-from' => [
                            200 => HubSpot::t("HubSpot Object synced successfully"),
                            400 => HubSpot::t("Failed to sync HubSpot Object")
                        ]
                    ]
                ]
            ]
        );
    }

    /**
     * @param string|null $id
     * @param string|null $field
     * @param string|null $elementType
     * @return ElementInterface|null
     * @throws HttpException
     * @throws \yii\base\InvalidConfigException
     * @throws \yii\web\BadRequestHttpException
     */
    public function actionSyncFrom(
        string $id = null,
        string $field = null,
        string $elementType = null
    ) {
        if ($id === null) {
            $id = Craft::$app->getRequest()->getRequiredParam('id');
        }

        if ($field === null) {
            $field = Craft::$app->getRequest()->getRequiredParam('field');
        }

        if ($elementType === null) {
            $elementType = Craft::$app->getRequest()->getRequiredParam('elementType');
        }

        /** @var SyncFrom $action */
        return (Craft::createObject([
            'class' => SyncFrom::class
        ], [
            'sync-from',
            $this
        ]))->runWithParams([
            'id' => $id,
            'field' => $field,
            'elementType' => $elementType
        ]);
    }
}
