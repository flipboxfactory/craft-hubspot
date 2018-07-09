<?php

/**
 * @copyright  Copyright (c) Flipbox Digital Limited
 * @license    https://flipboxfactory.com/software/hubspot/license
 * @link       https://www.flipboxfactory.com/software/hubspot/
 */

namespace flipbox\hubspot\criteria;

use flipbox\ember\helpers\ObjectHelper;
use flipbox\hubspot\HubSpot;

/**
 * @author Flipbox Factory <hello@flipboxfactory.com>
 * @since 1.0.0
 */
class ContactMutator extends ObjectMutator
{
    /**
     * @param array $config
     * @param null $source
     * @return mixed
     * @throws \yii\base\InvalidConfigException
     */
    public function create(array $config = [], $source = null)
    {
        $this->prepare($config);
        return HubSpot::getInstance()->getResources()->getContacts()->create($this, $source);
    }

    /**
     * @param array $config
     * @param null $source
     * @return mixed
     * @throws \yii\base\InvalidConfigException
     */
    public function update(array $config = [], $source = null)
    {
        $this->prepare($config);
        return HubSpot::getInstance()->getResources()->getContacts()->update($this, $source);
    }

    /**
     * @param array $config
     * @param null $source
     * @return mixed
     * @throws \yii\base\InvalidConfigException
     */
    public function delete(array $config = [], $source = null)
    {
        $this->prepare($config);
        return HubSpot::getInstance()->getResources()->getContacts()->delete($this, $source);
    }

    /**
     * @param array $config
     * @param null $source
     * @return mixed
     * @throws \yii\base\InvalidConfigException
     */
    public function upsert(array $config = [], $source = null)
    {
        $this->prepare($config);
        return HubSpot::getInstance()->getResources()->getContacts()->upsert($this, $source);
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
