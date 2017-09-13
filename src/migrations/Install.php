<?php

namespace flipbox\hubspot\migrations;

use craft\db\Migration as InstallMigration;
use flipbox\hubspot\HubSpot;

class Install extends InstallMigration
{
    /**
     * @inheritdoc
     */
    public function safeUp()
    {
        return $this->getContactSubMigration()->safeUp() &&
        $this->getCompanySubMigration()->safeUp();
    }

    /**
     * @inheritdoc
     */
    public function safeDown()
    {
        return $this->getContactSubMigration()->safeDown() &&
        $this->getCompanySubMigration()->safeDown();
    }

    /**
     * @return ObjectTable
     */
    private function getContactSubMigration()
    {
        return new ObjectTable([
            'tableAlias' => HubSpot::getInstance()->getSettings()->contactTableAlias
        ]);
    }

    /**
     * @return ObjectTable
     */
    private function getCompanySubMigration()
    {
        return new ObjectTable([
            'tableAlias' => HubSpot::getInstance()->getSettings()->companyTableAlias
        ]);
    }
}
