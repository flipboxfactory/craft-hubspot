<?php

namespace flipbox\hubspot\fields;

use craft\base\ElementInterface;
use flipbox\hubspot\HubSpot;

class ContactList extends AbstractField
{
    const FIELD_TYPE = 'ContactList';

    /**
     * @param ElementInterface $element
     * @return null|string
     */
    protected function findObjectId(ElementInterface $element)
    {
        return HubSpot::getInstance()->getContactList()->findContactListIdByElementId(
            $element->getId()
        );
    }

    /**
     * @inheritdoc
     */
    public function afterElementSave(ElementInterface $element, bool $isNew)
    {

        $value = $element->getFieldValue(
            $this->handle
        );

        // Remove existing associations
        HubSpot::getInstance()->getContactList()->disassociate($element);

        if ($value) {
            HubSpot::getInstance()->getContactList()->associateByIds(
                $element->getId(),
                $value
            );
        }

        parent::afterElementSave($element, $isNew);
    }
}
