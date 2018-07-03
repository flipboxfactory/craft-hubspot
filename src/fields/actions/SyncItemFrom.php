<?php

/**
 * @copyright  Copyright (c) Flipbox Digital Limited
 * @license    https://flipboxfactory.com/software/hubspot/license
 * @link       https://www.flipboxfactory.com/software/hubspot/
 */

namespace flipbox\hubspot\fields\actions;

use Craft;
use craft\base\ElementInterface;
use flipbox\hubspot\fields\Objects;
use flipbox\hubspot\records\ObjectAssociation;
use flipbox\hubspot\transformers\collections\TransformerCollection;

class SyncItemFrom extends AbstractObjectItemAction
{
    /**
     * @inheritdoc
     */
    public function getTriggerLabel(): string
    {
        return Craft::t('hubspot', 'Sync From HubSpot');
    }

    /**
     * @inheritdoc
     */
    public function getConfirmationMessage()
    {
        return Craft::t('hubspot', "Performing a sync will override any unsaved data.  Please confirm to continue.");
    }

    /**
     * @inheritdoc
     */
    public function performAction(Objects $field, ElementInterface $element, ObjectAssociation $record): bool
    {
        $resource = $field->getResource();

        if (!$resource->syncDown($element, $field, null, null, new TransformerCollection())) {
            $this->setMessage("Failed to sync from HubSpot Object");
            return false;
        }

        $this->setMessage("Sync from HubSpot executed successfully");
        return true;
    }
}
