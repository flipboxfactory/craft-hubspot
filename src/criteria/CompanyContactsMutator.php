<?php

/**
 * @copyright  Copyright (c) Flipbox Digital Limited
 * @license    https://flipboxfactory.com/software/hubspot/license
 * @link       https://www.flipboxfactory.com/software/hubspot/
 */

namespace flipbox\hubspot\criteria;

use flipbox\ember\helpers\ObjectHelper;
use flipbox\hubspot\HubSpot;
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

    /**
     * @param array $config
     * @param null $source
     * @return mixed
     * @throws \yii\base\InvalidConfigException
     */
    public function add(array $config = [], $source = null)
    {
        $this->prepare($config);
        return HubSpot::getInstance()->getResources()->getCompanyContacts()->add($this, $source);
    }

    /**
     * @param array $config
     * @param null $source
     * @return mixed
     * @throws \yii\base\InvalidConfigException
     */
    public function remove(array $config = [], $source = null)
    {
        $this->prepare($config);
        return HubSpot::getInstance()->getResources()->getCompanyContacts()->remove($this, $source);
    }

    /**
     * @inheritdoc
     */
    protected function prepare(array $criteria = [])
    {
        ObjectHelper::populate(
            $this,
            $criteria
        );
    }
}
