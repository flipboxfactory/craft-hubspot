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
use flipbox\craft\integration\fields\actions\AbstractIntegrationItemAction;
use flipbox\craft\integration\fields\Integrations;
use flipbox\craft\integration\records\IntegrationAssociation;

class SyncItemFrom extends AbstractIntegrationItemAction
{
    /**
     * @inheritdoc
     */
    public function getTriggerLabel(): string
    {
        return HubSpot::t('Sync From HubSpot');
    }

    /**
     * @inheritdoc
     */
    public function getConfirmationMessage()
    {
        return HubSpot::t("Performing a sync will override any unsaved data.  Please confirm to continue.");
    }

    /**
     * @param Integrations $field
     * @param ElementInterface $element
     * @param IntegrationAssociation $record
     * @return bool
     */
    public function performAction(Integrations $field, ElementInterface $element, IntegrationAssociation $record): bool
    {
        if (!$field instanceof ObjectsFieldInterface) {
            $this->setMessage("Invalid field type.");
            return false;
        }

        if (!$field->syncFromHubSpot($element)) {
            $this->setMessage("Failed to sync from HubSpot " . $field->getObjectLabel());
            return false;
        }

        $this->setMessage("Sync from HubSpot executed successfully");
        return true;
    }
}
