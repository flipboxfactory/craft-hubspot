<?php

namespace flipbox\hubspot\modules\http;

class Module extends \yii\base\Module
{
    /*******************************************
     * SERVICES
     *******************************************/

    /**
     * @return services\Contacts
     */
    public function contacts()
    {
        return $this->get('contacts');
    }

    /**
     * @return services\ContactLists
     */
    public function contactLists()
    {
        return $this->get('contactlists');
    }

    /**
     * @return services\Companies
     */
    public function companies()
    {
        return $this->get('companies');
    }
}
