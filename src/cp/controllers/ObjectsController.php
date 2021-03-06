<?php

/**
 * @copyright  Copyright (c) Flipbox Digital Limited
 * @license    https://flipboxfactory.com/software/hubspot/license
 * @link       https://www.flipboxfactory.com/software/hubspot/
 */

namespace flipbox\craft\hubspot\cp\controllers;

use Craft;
use craft\helpers\ArrayHelper;
use flipbox\craft\hubspot\actions\objects\AssociateObject;
use flipbox\craft\hubspot\actions\objects\DissociateObject;
use flipbox\craft\hubspot\HubSpot;
use flipbox\craft\hubspot\records\ObjectAssociation;

/**
 * @author Flipbox Factory <hello@flipboxfactory.com>
 * @since 1.0.0
 */
class ObjectsController extends AbstractController
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
                    'default' => 'element'
                ],
                'redirect' => [
                    'only' => ['associate', 'dissociate'],
                    'actions' => [
                        'associate' => [200],
                        'dissociate' => [200]
                    ]
                ],
                'flash' => [
                    'actions' => [
                        'associate' => [
                            200 => HubSpot::t("HubSpot Object associated successfully"),
                            400 => HubSpot::t("Failed to associate HubSpot Object")
                        ],
                        'dissociate' => [
                            200 => HubSpot::t("HubSpot Object dissociated successfully"),
                            400 => HubSpot::t("Failed to dissociate HubSpot Object")
                        ]
                    ]
                ]
            ]
        );
    }

    /**
     * @param string|null $newObjectId
     * @param string|null $objectId
     * @param string|null $field
     * @param string|null $element
     * @return ObjectAssociation|null
     * @throws \yii\base\InvalidConfigException
     * @throws \yii\web\BadRequestHttpException
     */
    public function actionAssociate(
        string $newObjectId = null,
        string $objectId = null,
        string $field = null,
        string $element = null
    ) {

        if ($objectId === null) {
            $objectId = Craft::$app->getRequest()->getParam('objectId');
        }

        if ($newObjectId === null) {
            $newObjectId = Craft::$app->getRequest()->getRequiredParam('newObjectId');
        }

        if ($field === null) {
            $field = Craft::$app->getRequest()->getRequiredParam('field');
        }

        if ($element === null) {
            $element = Craft::$app->getRequest()->getRequiredParam('element');
        }

        /** @var AssociateObject $action */
        return (Craft::createObject([
            'class' => AssociateObject::class
        ], [
            'associate',
            $this
        ]))->runWithParams([
            'field' => $field,
            'element' => $element,
            'objectId' => $objectId,
            'newObjectId' => $newObjectId
        ]);
    }

    /**
     * @param string|null $objectId
     * @param string|null $field
     * @param string|null $element
     * @return ObjectAssociation|null
     * @throws \yii\base\InvalidConfigException
     * @throws \yii\web\BadRequestHttpException
     */
    public function actionDissociate(
        string $objectId = null,
        string $field = null,
        string $element = null
    ) {

        if ($objectId === null) {
            $objectId = Craft::$app->getRequest()->getRequiredParam('objectId');
        }

        if ($field === null) {
            $field = Craft::$app->getRequest()->getRequiredParam('field');
        }

        if ($element === null) {
            $element = Craft::$app->getRequest()->getRequiredParam('element');
        }

        /** @var DissociateObject $action */
        return (Craft::createObject([
            'class' => DissociateObject::class
        ], [
            'dissociate',
            $this
        ]))->runWithParams([
            'field' => $field,
            'element' => $element,
            'objectId' => $objectId
        ]);
    }
}
