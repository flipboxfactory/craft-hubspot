<?php

/**
 * @copyright  Copyright (c) Flipbox Digital Limited
 * @license    https://flipboxfactory.com/software/hubspot/license
 * @link       https://www.flipboxfactory.com/software/hubspot/
 */

namespace flipbox\hubspot\criteria;

use yii\base\BaseObject;

/**
 * @author Flipbox Factory <hello@flipboxfactory.com>
 * @since 1.0.0
 */
class CompanyContactsMutator extends BaseObject implements CompanyContactsMutatorInterface
{
    use traits\TransformerCollectionTrait,
        traits\ConnectionTrait,
        traits\CacheTrait;

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
