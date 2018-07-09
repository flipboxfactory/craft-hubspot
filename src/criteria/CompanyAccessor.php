<?php

/**
 * @copyright  Copyright (c) Flipbox Digital Limited
 * @license    https://flipboxfactory.com/software/hubspot/license
 * @link       https://www.flipboxfactory.com/software/hubspot/
 */

namespace flipbox\hubspot\criteria;

use flipbox\ember\helpers\ObjectHelper;
use flipbox\hubspot\HubSpot;
use flipbox\hubspot\services\resources\Companies;

/**
 * @author Flipbox Factory <hello@flipboxfactory.com>
 * @since 1.0.0
 */
class CompanyAccessor extends ObjectAccessor
{
    /**
     * @inheritdoc
     */
    public function init()
    {
        $this->transformer = Companies::defaultTransformer();
        parent::init();
    }

    /**
     * @param array $config
     * @param null $source
     * @return mixed
     * @throws \yii\base\InvalidConfigException
     */
    public function read(array $config = [], $source = null)
    {
        $this->prepare($config);
        return HubSpot::getInstance()->getResources()->getCompanies()->read($this, $source);
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
