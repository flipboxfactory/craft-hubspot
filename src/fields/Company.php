<?php

namespace flipbox\hubspot\fields;

use craft\base\ElementInterface;
use flipbox\hubspot\HubSpot;

class Company extends AbstractField
{
    const FIELD_TYPE = 'Company';

    /**
     * @param ElementInterface $element
     * @return null|string
     */
    protected function findObjectId(ElementInterface $element)
    {
        return HubSpot::getInstance()->getCompany()->findCompanyIdByElementId(
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
        HubSpot::getInstance()->getCompany()->disassociate($element);

        if ($value) {
            HubSpot::getInstance()->getCompany()->associateByIds(
                $element->getId(),
                $value
            );
        }

        parent::afterElementSave($element, $isNew);
    }
}
