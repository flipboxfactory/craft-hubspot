<?php

/**
 * @copyright  Copyright (c) Flipbox Digital Limited
 * @license    https://flipboxfactory.com/software/hubspot/license
 * @link       https://www.flipboxfactory.com/software/hubspot/
 */

namespace flipbox\hubspot\cp\actions\fields;

use craft\base\ElementInterface;
use flipbox\ember\actions\traits\Manage;
use flipbox\hubspot\actions\traits\ElementResolverTrait;
use flipbox\hubspot\actions\traits\FieldResolverTrait;
use flipbox\hubspot\fields\actions\ObjectActionInterface;
use flipbox\hubspot\fields\Objects;
use flipbox\hubspot\HubSpot;
use yii\base\Action;
use yii\web\HttpException;

/**
 * @author Flipbox Factory <hello@flipboxfactory.com>
 * @since 1.0.0
 */
class PerformAction extends Action
{
    use ElementResolverTrait,
        FieldResolverTrait,
        Manage;

    /**
     * @param string $field
     * @param string $element
     * @param string|null $action
     * @return mixed
     * @throws HttpException
     * @throws \craft\errors\MissingComponentException
     * @throws \yii\base\InvalidConfigException
     */
    public function run(string $field, string $element, string $action = null)
    {
        $field = $this->resolveField($field);
        $element = $this->resolveElement($element);

        $availableActions = HubSpot::getInstance()->getObjectsField()->getActions($field);

        foreach ($availableActions as $availableAction) {
            if ($action === get_class($availableAction)) {
                $action = $availableAction;
                break;
            }
        }

        if (!$action instanceof ObjectActionInterface) {
            throw new HttpException(400, 'Field action is not supported by the field');
        }

        return $this->runInternal($action, $field, $element);
    }

    /**
     * @param ObjectActionInterface $action
     * @param Objects $field
     * @param ElementInterface $element
     * @return mixed
     * @throws \yii\web\UnauthorizedHttpException
     */
    protected function runInternal(
        ObjectActionInterface $action,
        Objects $field,
        ElementInterface $element
    ) {
        // Check access
        if (($access = $this->checkAccess($action, $field, $element)) !== true) {
            return $access;
        }

        if (!$this->performAction($action, $field, $element)) {
            return $this->handleFailResponse($action);
        }

        return $this->handleSuccessResponse($action);
    }

    /**
     * @param ObjectActionInterface $action
     * @param Objects $field
     * @param ElementInterface $element
     * @return bool
     */
    public function performAction(
        ObjectActionInterface $action,
        Objects $field,
        ElementInterface $element
    ): bool {
        return $action->performAction($field, $element);
    }
}
