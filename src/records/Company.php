<?php

namespace flipbox\hubspot\records;

use flipbox\hubspot\HubSpot;

class Company extends AbstractObject
{
    /**
     * The table alias
     */
    const TABLE_ALIAS = AbstractObject::TABLE_ALIAS . '_company';

    /**
     * {@inheritdoc}
     */
    public static function tableAlias()
    {
        return HubSpot::getInstance()->getSettings()->companyTableAlias;
    }
}
