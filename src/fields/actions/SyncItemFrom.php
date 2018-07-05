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
     * @throws \yii\base\InvalidConfigException
     */
    public function performAction(Objects $field, ElementInterface $element, ObjectAssociation $record): bool
    {
        if (!$field->getResource()->syncDown($element, $field)) {
            $this->setMessage("Failed to sync from HubSpot Object");
            return false;
        }

        $this->setMessage("Sync from HubSpot executed successfully");
        return true;
    }
}
