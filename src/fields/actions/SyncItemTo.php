<?php

/**
 * @copyright  Copyright (c) Flipbox Digital Limited
 * @license    https://flipboxfactory.com/software/hubspot/license
 * @link       https://www.flipboxfactory.com/software/hubspot/
 */

namespace flipbox\hubspot\fields\actions;

use Craft;
use craft\base\ElementInterface;
use flipbox\craft\integration\fields\actions\AbstractIntegrationItemAction;
use flipbox\craft\integration\fields\Integrations;
use flipbox\craft\integration\records\IntegrationAssociation;

class SyncItemTo extends AbstractIntegrationItemAction
{
    /**
     * @inheritdoc
     */
    public function getTriggerLabel(): string
    {
        return Craft::t('hubspot', 'Sync To HubSpot');
    }

    /**
     * @inheritdoc
     */
    public function getConfirmationMessage()
    {
        return Craft::t('hubspot', "Performing a sync will transmit any unsaved data.  Please confirm to continue.");
    }

    /**
     * @inheritdoc
     * @throws \yii\base\InvalidConfigException
     */
    public function performAction(Integrations $field, ElementInterface $element, IntegrationAssociation $record): bool
    {
        if (!$field->getResource()->syncUp($element, $field)) {
            $this->setMessage("Failed to sync to HubSpot Object");
            return false;
        }

        $this->setMessage("Sync to HubSpot executed successfully");
        return true;
    }
}
