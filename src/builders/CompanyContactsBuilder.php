<?php

/**
 * @copyright  Copyright (c) Flipbox Digital Limited
 * @license    https://flipboxfactory.com/software/hubspot/license
 * @link       https://www.flipboxfactory.com/software/hubspot/
 */

namespace flipbox\hubspot\builders;

use yii\base\BaseObject;

/**
 * @author Flipbox Factory <hello@flipboxfactory.com>
 * @since 1.0.0
 */
class CompanyContactsBuilder extends BaseObject implements CompanyContactsBuilderInterface
{
    /**
     * @var string
     */
    public $companyId;

    /**
     * @var string
     */
    public $contactId;

    /**
     * @return string
     */
    public function getCompanyId(): string
    {
        return (string)$this->companyId;
    }

    /**
     * @return string
     */
    public function getContactId(): string
    {
        return (string)$this->contactId;
    }
}