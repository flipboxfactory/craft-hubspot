<?php

namespace flipbox\hubspot\fields;

use craft\base\ElementInterface;
use flipbox\hubspot\HubSpot;

class Contact extends AbstractField
{
    const FIELD_TYPE = 'Contact';

    /**
     * @param ElementInterface $element
     * @return null|string
     */
    protected function findObjectId(ElementInterface $element)
    {
        return HubSpot::getInstance()->getContact()->findContactIdByElementId(
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
        HubSpot::getInstance()->getContact()->disassociate($element);

        if ($value) {
            HubSpot::getInstance()->getContact()->associateByIds(
                $element->getId(),
                $value
            );
        }

        parent::afterElementSave($element, $isNew);
    }
}
