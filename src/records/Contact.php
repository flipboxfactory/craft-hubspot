<?php

namespace flipbox\hubspot\records;

use flipbox\hubspot\HubSpot;

class Contact extends AbstractObject
{
    /**
     * The table alias
     */
    const TABLE_ALIAS = AbstractObject::TABLE_ALIAS . '_contact';

    /**
     * {@inheritdoc}
     */
    public static function tableAlias()
    {
        return HubSpot::getInstance()->getSettings()->contactTableAlias;
    }
}
