<?php

/**
 * @copyright  Copyright (c) Flipbox Digital Limited
 * @license    https://flipboxfactory.com/software/hubspot/license
 * @link       https://www.flipboxfactory.com/software/hubspot/
 */

namespace flipbox\craft\hubspot\cp\actions\sync;

use Craft;
use craft\base\ElementInterface;
use flipbox\craft\ember\actions\CheckAccessTrait;
use flipbox\craft\hubspot\fields\Objects;
use yii\base\Action;

/**
 * @author Flipbox Factory <hello@flipboxfactory.com>
 * @since 1.0.0
 */
abstract class AbstractSyncFrom extends Action
{
    use CheckAccessTrait;

    /**
     * @param ElementInterface $element
     * @param Objects $field
     * @param string|null $objectId
     * @return ElementInterface|mixed
     * @throws \Throwable
     * @throws \craft\errors\ElementNotFoundException
     * @throws \yii\base\Exception
     * @throws \yii\web\UnauthorizedHttpException
     */
    protected function runInternal(
        ElementInterface $element,
        Objects $field,
        string $objectId = null
    ) {
        // Check access
        if (($access = $this->checkAccess($element, $field, $objectId)) !== true) {
            return $access;
        }

        if (false === $this->performAction($element, $field, $objectId)) {
            return $this->handleFailResponse($element);
        }

        return $this->handleSuccessResponse($element);
    }

    /**
     * @param ElementInterface $element
     * @param Objects $field
     * @param string|null $objectId
     * @return bool
     * @throws \Throwable
     * @throws \craft\errors\ElementNotFoundException
     * @throws \yii\base\Exception
     */
    protected function performAction(
        ElementInterface $element,
        Objects $field,
        string $objectId = null
    ) {
        return $field->syncFromHubSpot($element, $objectId);
    }

    /**
     * @param ElementInterface $element
     * @return ElementInterface
     */
    protected function handleSuccessResponse(ElementInterface $element)
    {
        Craft::$app->getResponse()->setStatusCode(200);
        return $element;
    }

    /**
     * @param ElementInterface $element
     * @return ElementInterface
     */
    protected function handleFailResponse(ElementInterface $element)
    {
        Craft::$app->getResponse()->setStatusCode(400);
        return $element;
    }
}
