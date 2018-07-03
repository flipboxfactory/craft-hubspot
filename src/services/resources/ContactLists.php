<?php

/**
 * @copyright  Copyright (c) Flipbox Digital Limited
 * @license    https://flipboxfactory.com/software/hubspot/license
 * @link       https://www.flipboxfactory.com/software/hubspot/
 */

namespace flipbox\hubspot\services\resources;

use flipbox\hubspot\builders\ContactListBuilder;
use flipbox\hubspot\builders\ObjectBuilderInterface;
use flipbox\hubspot\criteria\ContactListCriteria;
use flipbox\hubspot\criteria\ObjectCriteriaInterface;
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
    use traits\SyncByElementTrait,
        traits\ReadObjectTrait,
        traits\UpsertObjectTrait,
        traits\DeleteObjectTrait;

    /**
     * The HubSpot Resource name
     */
    const HUBSPOT_RESOURCE = 'contactLists';

    /**
     * @param array $config
     * @return ObjectCriteriaInterface
     */
    public function getCriteria(array $config = []): ObjectCriteriaInterface
    {
        return new ContactListCriteria($config);
    }

    /**
     * @param array $config
     * @return ObjectBuilderInterface
     */
    public function getBuilder(array $config = []): ObjectBuilderInterface
    {
        return new ContactListBuilder($config);
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
