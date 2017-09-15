<?php

namespace flipbox\hubspot\records;

use flipbox\hubspot\HubSpot;

class ContactList extends AbstractObject
{
    /**
     * The table alias
     */
    const TABLE_ALIAS = AbstractObject::TABLE_ALIAS . '_contactlist';

    /**
     * {@inheritdoc}
     */
    public static function tableAlias()
    {
        return HubSpot::getInstance()->getSettings()->contactListTableAlias;
    }
}
