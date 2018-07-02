<?php

/**
 * @copyright  Copyright (c) Flipbox Digital Limited
 * @license    https://flipboxfactory.com/software/hubspot/license
 * @link       https://www.flipboxfactory.com/software/hubspot/
 */

namespace flipbox\hubspot\criteria;

use flipbox\hubspot\HubSpot;
use flipbox\hubspot\services\resources\ContactLists;
use flipbox\hubspot\transformers\collections\DynamicTransformerCollection;
use flipbox\hubspot\transformers\collections\TransformerCollectionInterface;
use flipbox\hubspot\transformers\DynamicModelSuccess;

/**
 * @author Flipbox Factory <hello@flipboxfactory.com>
 * @since 1.0.0
 */
class ContactListCriteria extends ObjectCriteria
{
    /**
     * @inheritdoc
     */
    protected $transformer = [
        'class' => DynamicTransformerCollection::class,
        'handle' => ContactLists::HUBSPOT_RESOURCE,
        'transformers' => [
            TransformerCollectionInterface::SUCCESS_KEY => [
                'class' => DynamicModelSuccess::class,
                'resource' => ContactLists::HUBSPOT_RESOURCE
            ]
        ]
    ];

    /**
     * @inheritdoc
     * @throws \yii\base\InvalidConfigException
     */
    public function fetch(array $config = [])
    {
        $this->prepare($config);

        return HubSpot::getInstance()
            ->getResources()
            ->getContactLists()
            ->read($this);
    }
}
