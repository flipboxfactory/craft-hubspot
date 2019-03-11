<?php

/**
 * @copyright  Copyright (c) Flipbox Digital Limited
 * @license    https://flipboxfactory.com/software/hubspot/license
 * @link       https://www.flipboxfactory.com/software/hubspot/
 */

namespace flipbox\craft\hubspot\fields\actions;

use craft\base\ElementInterface;
use flipbox\craft\hubspot\fields\ObjectsFieldInterface;
use flipbox\craft\hubspot\HubSpot;
use flipbox\craft\integration\fields\actions\AbstractIntegrationAction;
use flipbox\craft\integration\fields\Integrations;
use flipbox\craft\integration\queries\IntegrationAssociationQuery;
use yii\web\HttpException;

class SyncTo extends AbstractIntegrationAction
{
    /**
     * @inheritdoc
     */
    public function getTriggerLabel(): string
    {
        return HubSpot::t('Create HubSpot Object from Element');
    }

    /**
     * @inheritdoc
     */
    public function getConfirmationMessage()
    {
        return HubSpot::t(
            "This element will be used to create a new HubSpot Object.  Please confirm to continue."
        );
    }

    /**
     * @inheritdoc
     * @throws HttpException
     * @throws \Throwable
     */
    public function performAction(Integrations $field, ElementInterface $element): bool
    {
        if (!$field instanceof ObjectsFieldInterface) {
            $this->setMessage("Invalid field type.");
            return false;
        }

        /** @var IntegrationAssociationQuery $query */
        if (null === ($query = $element->getFieldValue($field->handle))) {
            throw new HttpException(400, 'Field is not associated to element');
        }

        if (!$field->syncToHubSpot($element)) {
            $this->setMessage("Failed to sync from HubSpot Object");
            return false;
        }

        // Reset
        $element->setFieldValue($field->handle, null);

        $this->id = $query->select(['objectId'])->scalar();

        $this->setMessage("Sync to HubSpot executed successfully");
        return true;
    }
}
