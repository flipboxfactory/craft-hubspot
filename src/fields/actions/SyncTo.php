<?php

/**
 * @copyright  Copyright (c) Flipbox Digital Limited
 * @license    https://flipboxfactory.com/software/hubspot/license
 * @link       https://www.flipboxfactory.com/software/hubspot/
 */

namespace flipbox\hubspot\fields\actions;

use Craft;
use craft\base\ElementInterface;
use flipbox\hubspot\db\ObjectAssociationQuery;
use flipbox\hubspot\fields\Objects;
use yii\web\HttpException;

class SyncTo extends AbstractObjectAction
{
    /**
     * @inheritdoc
     */
    public function getTriggerLabel(): string
    {
        return Craft::t('hubspot', 'Create HubSpot Object from Element');
    }

    /**
     * @inheritdoc
     */
    public function getConfirmationMessage()
    {
        return Craft::t(
            'hubspot',
            "This element will be used to create a new HubSpot Object.  Please confirm to continue."
        );
    }

    /**
     * @inheritdoc
     * @throws HttpException
     * @throws \yii\base\InvalidConfigException
     */
    public function performAction(Objects $field, ElementInterface $element): bool
    {
        /** @var ObjectAssociationQuery $query */
        if (null === ($query = $element->getFieldValue($field->handle))) {
            throw new HttpException(400, 'Field is not associated to element');
        }

        $resource = $field->getResource();

        if (!$resource->syncUp($element, $field)) {
            $this->setMessage("Failed to sync from HubSpot Object");
            return false;
        }

        $element->setFieldValue($field->handle, null);

        $this->id = $query->select(['objectId'])->scalar();

        $this->setMessage("Sync to HubSpot executed successfully");
        return true;
    }
}
