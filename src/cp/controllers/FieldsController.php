<?php

/**
 * @copyright  Copyright (c) Flipbox Digital Limited
 * @license    https://flipboxfactory.com/software/hubspot/license
 * @link       https://www.flipboxfactory.com/software/hubspot/
 */

namespace flipbox\craft\hubspot\cp\controllers;

use Craft;
use craft\helpers\ArrayHelper;
use flipbox\craft\hubspot\HubSpot;
use flipbox\craft\integration\actions\fields\CreateFieldItem;
use flipbox\craft\integration\actions\fields\PerformFieldAction;
use flipbox\craft\integration\actions\fields\PerformFieldItemAction;
use yii\web\BadRequestHttpException;

/**
 * @author Flipbox Factory <hello@flipboxfactory.com>
 * @since 1.0.0
 */
class FieldsController extends AbstractController
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
                    'default' => 'action'
                ],
                'redirect' => [
                    'only' => ['perform-action'],
                    'actions' => [
                        'perform-action' => [200]
                    ]
                ],
                'flash' => [
                    'actions' => [
                        'perform-action' => [
                            200 => HubSpot::t("Action executed successfully."),
                            400 => HubSpot::t("Failed to execute action.")
                        ]
                    ]
                ]
            ]
        );
    }

    /**
     * Executes a Field Action.
     *
     * @param string|null $field
     * @param string|null $element
     * @param string|null $action
     * @param string|null $id
     * @return mixed
     * @throws BadRequestHttpException
     * @throws \yii\base\InvalidConfigException
     */
    public function actionPerformItemAction(
        string $field = null,
        string $element = null,
        string $action = null,
        string $id = null
    ) {
        if ($field === null) {
            $field = Craft::$app->getRequest()->getRequiredParam('field');
        }

        if ($element === null) {
            $element = Craft::$app->getRequest()->getRequiredParam('element');
        }

        if ($action === null) {
            $action = Craft::$app->getRequest()->getRequiredParam('action');
        }

        if ($id === null) {
            $id = Craft::$app->getRequest()->getRequiredParam('id');
        }

        /** @var PerformFieldItemAction $action */
        return (Craft::createObject([
            'class' => PerformFieldItemAction::class
        ], [
            'preform-action',
            $this
        ]))->runWithParams([
            'field' => $field,
            'element' => $element,
            'action' => $action,
            'id' => $id
        ]);
    }

    /**
     * Executes a Field Action.
     *
     * @param string|null $field
     * @param string|null $element
     * @param string|null $action
     * @return mixed
     * @throws BadRequestHttpException
     * @throws \yii\base\InvalidConfigException
     */
    public function actionPerformAction(
        string $field = null,
        string $element = null,
        string $action = null
    ) {
        if ($field === null) {
            $field = Craft::$app->getRequest()->getRequiredParam('field');
        }

        if ($element === null) {
            $element = Craft::$app->getRequest()->getRequiredParam('element');
        }

        if ($action === null) {
            $action = Craft::$app->getRequest()->getRequiredParam('action');
        }

        /** @var PerformFieldAction $action */
        return (Craft::createObject([
            'class' => PerformFieldAction::class
        ], [
            'preform-action',
            $this
        ]))->runWithParams([
            'field' => $field,
            'element' => $element,
            'action' => $action
        ]);
    }

    /**
     * @param string|null $field
     * @param string|null $element
     * @param string|null $id
     * @return mixed
     * @throws BadRequestHttpException
     * @throws \yii\base\InvalidConfigException
     */
    public function actionCreateItem(
        string $field = null,
        string $element = null,
        string $id = null
    ) {
        if ($field === null) {
            $field = Craft::$app->getRequest()->getRequiredParam('field');
        }

        if ($element === null) {
            $element = Craft::$app->getRequest()->getRequiredParam('element');
        }

        if ($id === null) {
            $id = Craft::$app->getRequest()->getParam('id');
        }

        /** @var CreateFieldItem $action */
        return (Craft::createObject([
            'class' => CreateFieldItem::class
        ], [
            'create-row',
            $this
        ]))->runWithParams([
            'field' => $field,
            'element' => $element,
            'id' => $id
        ]);
    }
}
