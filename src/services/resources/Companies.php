<?php

/**
 * @copyright  Copyright (c) Flipbox Digital Limited
 * @license    https://flipboxfactory.com/software/hubspot/license
 * @link       https://www.flipboxfactory.com/software/hubspot/
 */

namespace flipbox\hubspot\services\resources;

use flipbox\hubspot\criteria\CompanyMutator;
use flipbox\hubspot\criteria\ObjectMutatorInterface;
use flipbox\hubspot\criteria\CompanyAccessor;
use flipbox\hubspot\criteria\ObjectAccessorInterface;
use flipbox\hubspot\transformers\collections\DynamicTransformerCollection;
use flipbox\hubspot\transformers\collections\TransformerCollectionInterface;
use flipbox\hubspot\transformers\DynamicModelSuccess;
use Flipbox\Relay\HubSpot\Builder\Resources\Company\Create;
use Flipbox\Relay\HubSpot\Builder\Resources\Company\Delete;
use Flipbox\Relay\HubSpot\Builder\Resources\Company\Read;
use Flipbox\Relay\HubSpot\Builder\Resources\Company\Update;
use yii\base\Component;

/**
 * @author Flipbox Factory <hello@flipboxfactory.com>
 * @since 1.0.0
 */
class Companies extends Component implements CRUDInterface
{
    use traits\SyncByElementTrait,
        traits\ReadObjectTrait,
        traits\UpsertObjectTrait,
        traits\DeleteObjectTrait;

    /**
     * The HubSpot Resource name
     */
    const HUBSPOT_RESOURCE = 'companies';

    /**
     * @return array
     */
    public static function defaultTransformer()
    {
        return [
            'class' => DynamicTransformerCollection::class,
            'handle' => self::HUBSPOT_RESOURCE,
            'transformers' => [
                TransformerCollectionInterface::SUCCESS_KEY => [
                    'class' => DynamicModelSuccess::class,
                    'resource' => self::HUBSPOT_RESOURCE
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
        return new CompanyAccessor($config);
    }

    /**
     * @param array $config
     * @return ObjectMutatorInterface
     */
    public function getMutatorCriteria(array $config = []): ObjectMutatorInterface
    {
        return new CompanyMutator($config);
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
