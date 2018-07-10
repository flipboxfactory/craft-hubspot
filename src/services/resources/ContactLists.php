<?php

/**
 * @copyright  Copyright (c) Flipbox Digital Limited
 * @license    https://flipboxfactory.com/software/hubspot/license
 * @link       https://www.flipboxfactory.com/software/hubspot/
 */

namespace flipbox\hubspot\services\resources;

use flipbox\hubspot\criteria\ContactListAccessor;
use flipbox\hubspot\criteria\ContactListMutator;
use flipbox\hubspot\criteria\ObjectAccessorInterface;
use flipbox\hubspot\criteria\ObjectMutatorInterface;
use flipbox\hubspot\transformers\collections\DynamicTransformerCollection;
use flipbox\hubspot\transformers\collections\TransformerCollectionInterface;
use flipbox\hubspot\transformers\DynamicModelSuccess;
use Flipbox\Relay\HubSpot\Builder\Resources\ContactList\Create;
use Flipbox\Relay\HubSpot\Builder\Resources\ContactList\Delete;
use Flipbox\Relay\HubSpot\Builder\Resources\ContactList\Read;
use Flipbox\Relay\HubSpot\Builder\Resources\ContactList\Update;
use yii\base\Component;

/**
 * @author Flipbox Factory <hello@flipboxfactory.com>
 * @since 1.0.0
 */
class ContactLists extends Component implements CRUDInterface
{
    use traits\SyncElementTrait,
        traits\ReadObjectTrait,
        traits\UpsertObjectTrait,
        traits\DeleteObjectTrait;

    /**
     * The HubSpot Resource name
     */
    const HUBSPOT_RESOURCE = 'contactLists';

    /**
     * @inheritdoc
     */
    public static function defaultTransformer()
    {
        return [
            'class' => DynamicTransformerCollection::class,
            'handle' => ContactLists::HUBSPOT_RESOURCE,
            'transformers' => [
                TransformerCollectionInterface::SUCCESS_KEY => [
                    'class' => DynamicModelSuccess::class,
                    'resource' => ContactLists::HUBSPOT_RESOURCE
                ]
            ]
        ];
    }

    /**
     * @param array $config
     * @return ObjectAccessorInterface
     */
    public function getAccessorCriteria(array $config = []): ObjectAccessorInterface
    {
        return new ContactListAccessor($config);
    }

    /**
     * @param array $config
     * @return ObjectMutatorInterface
     */
    public function getMutatorCriteria(array $config = []): ObjectMutatorInterface
    {
        return new ContactListMutator($config);
    }

    /**
     * @inheritdoc
     */
    protected static function createRelayBuilderClass(): string
    {
        return Create::class;
    }

    /**
     * @inheritdoc
     */
    protected static function readRelayBuilderClass(): string
    {
        return Read::class;
    }

    /**
     * @inheritdoc
     */
    protected static function updateRelayBuilderClass(): string
    {
        return Update::class;
    }

    /**
     * @inheritdoc
     */
    protected static function deleteRelayBuilderClass(): string
    {
        return Delete::class;
    }
}
