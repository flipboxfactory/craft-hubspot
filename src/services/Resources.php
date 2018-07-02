<?php

/**
 * @copyright  Copyright (c) Flipbox Digital Limited
 * @license    https://flipboxfactory.com/software/hubspot/license
 * @link       https://www.flipboxfactory.com/software/hubspot/
 */

namespace flipbox\hubspot\services;

use yii\di\ServiceLocator;

/**
 * @author Flipbox Factory <hello@flipboxfactory.com>
 * @since 1.0.0
 */
class Resources extends ServiceLocator
{
    /**
     * @inheritdoc
     */
    public function init()
    {
        parent::init();

        $this->setComponents([
            resources\Companies::HUBSPOT_RESOURCE => resources\Companies::class,
            resources\CompanyContacts::HUBSPOT_RESOURCE => resources\CompanyContacts::class,
            resources\Contacts::HUBSPOT_RESOURCE => resources\Contacts::class,
            resources\ContactLists::HUBSPOT_RESOURCE => resources\ContactLists::class,
            resources\ContactListContacts::HUBSPOT_RESOURCE => resources\ContactListContacts::class,
            resources\TimelineEvents::HUBSPOT_RESOURCE => resources\TimelineEvents::class
        ]);
    }

    /**
     * @noinspection PhpDocMissingThrowsInspection
     * @return resources\Companies
     */
    public function getCompanies(): resources\Companies
    {
        /** @noinspection PhpUnhandledExceptionInspection */
        /** @noinspection PhpIncompatibleReturnTypeInspection */
        return $this->get(resources\Companies::HUBSPOT_RESOURCE);
    }

    /**
     * @noinspection PhpDocMissingThrowsInspection
     * @return resources\CompanyContacts
     */
    public function getCompanyContacts(): resources\CompanyContacts
    {
        /** @noinspection PhpUnhandledExceptionInspection */
        /** @noinspection PhpIncompatibleReturnTypeInspection */
        return $this->get(resources\CompanyContacts::HUBSPOT_RESOURCE);
    }

    /**
     * @noinspection PhpDocMissingThrowsInspection
     * @return resources\Contacts
     */
    public function getContacts(): resources\Contacts
    {
        /** @noinspection PhpUnhandledExceptionInspection */
        /** @noinspection PhpIncompatibleReturnTypeInspection */
        return $this->get(resources\Contacts::HUBSPOT_RESOURCE);
    }

    /**
     * @noinspection PhpDocMissingThrowsInspection
     * @return resources\ContactLists
     */
    public function getContactLists(): resources\ContactLists
    {
        /** @noinspection PhpUnhandledExceptionInspection */
        /** @noinspection PhpIncompatibleReturnTypeInspection */
        return $this->get(resources\ContactLists::HUBSPOT_RESOURCE);
    }

    /**
     * @noinspection PhpDocMissingThrowsInspection
     * @return resources\ContactListContacts
     */
    public function getContactListContacts(): resources\ContactListContacts
    {
        /** @noinspection PhpUnhandledExceptionInspection */
        /** @noinspection PhpIncompatibleReturnTypeInspection */
        return $this->get(resources\ContactListContacts::HUBSPOT_RESOURCE);
    }

    /**
     * @noinspection PhpDocMissingThrowsInspection
     * @return resources\TimelineEvents
     */
    public function getTimelineEvents(): resources\TimelineEvents
    {
        /** @noinspection PhpUnhandledExceptionInspection */
        /** @noinspection PhpIncompatibleReturnTypeInspection */
        return $this->get(resources\TimelineEvents::HUBSPOT_RESOURCE);
    }
}
