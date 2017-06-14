<?php

namespace flipbox\hubspot\modules\resources;

class Module extends \yii\base\Module
{

    /*******************************************
     * SERVICES
     *******************************************/

    /**
     * @return services\Contacts
     */
    public function getContacts()
    {
        return $this->get('contacts');
    }

    /**
     * @return services\Companies
     */
    public function getCompanies()
    {
        return $this->get('companies');
    }
}
