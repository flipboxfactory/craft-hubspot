<?php

/**
 * @copyright  Copyright (c) Flipbox Digital Limited
 * @license    https://flipboxfactory.com/software/hubspot/license
 * @link       https://www.flipboxfactory.com/software/hubspot/
 */

namespace flipbox\hubspot\fields\actions;

use Craft;
use craft\base\ElementInterface;
use flipbox\force\criteria\SObjectCriteria;
use flipbox\hubspot\records\ObjectAssociation;
use flipbox\force\fields\SObjects;
use flipbox\force\Force;
use flipbox\hubspot\fields\Objects;
use flipbox\hubspot\HubSpot;
use flipbox\hubspot\transformers\collections\TransformerCollection;

class SyncItemTo extends AbstractObjectItemAction
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
     */
    public function performAction(Objects $field, ElementInterface $element, ObjectAssociation $record): bool
    {
        $resource = $field->getResource();

        if (!$resource->syncUp($element, $field, null, null, new TransformerCollection())) {
            $this->setMessage("Failed to sync to HubSpot Object");
            return false;
        }

        $this->setMessage("Sync to HubSpot executed successfully");
        return true;
    }
}
