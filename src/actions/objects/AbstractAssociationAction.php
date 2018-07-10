<?php

/**
 * @copyright  Copyright (c) Flipbox Digital Limited
 * @license    https://flipboxfactory.com/software/hubspot/license
 * @link       https://www.flipboxfactory.com/software/hubspot/
 */

namespace flipbox\hubspot\actions\objects;

use craft\base\ElementInterface;
use flipbox\ember\actions\model\traits\Manage;
use flipbox\ember\exceptions\RecordNotFoundException;
use flipbox\hubspot\fields\Objects;
use flipbox\hubspot\HubSpot;
use flipbox\hubspot\records\ObjectAssociation;
use yii\base\Action;
use yii\base\Model;
use yii\web\HttpException;

/**
 * @author Flipbox Factory <hello@flipboxfactory.com>
 * @since  1.0.0
 */
abstract class AbstractAssociationAction extends Action
{
    use Manage;

    /**
     * @param string $field
     * @return Objects
     * @throws HttpException
     */
    protected function resolveField(string $field): Objects
    {
        if (null === ($resourcesField = HubSpot::getInstance()->getObjectsField()->findById($field))) {
            return $this->handleInvalidFieldResponse($field);
        }

        return $resourcesField;
    }

    /**
     * @param Model $model
     * @return bool
     * @throws RecordNotFoundException
     */
    protected function ensureAssociation(Model $model): bool
    {
        if (!$model instanceof ObjectAssociation) {
            throw new RecordNotFoundException(sprintf(
                "HubSpot Resource Association must be an instance of '%s', '%s' given.",
                ObjectAssociation::class,
                get_class($model)
            ));
        }

        return true;
    }

    /**
     * @param int $fieldId
     * @throws HttpException
     */
    protected function handleInvalidFieldResponse(int $fieldId)
    {
        throw new HttpException(sprintf(
            "The provided field '%s' must be an instance of '%s'",
            (string)$fieldId,
            (string)Objects::class
        ));
    }

    /**
     * @param int $elementId
     * @throws HttpException
     */
    protected function handleInvalidElementResponse(int $elementId)
    {
        throw new HttpException(sprintf(
            "The provided source '%s' must be an instance of '%s'",
            (string)$elementId,
            (string)ElementInterface::class
        ));
    }
}
