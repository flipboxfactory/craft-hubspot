<?php

/**
 * @copyright  Copyright (c) Flipbox Digital Limited
 * @license    https://flipboxfactory.com/software/hubspot/license
 * @link       https://www.flipboxfactory.com/software/hubspot/
 */

namespace flipbox\hubspot\services\resources;

use flipbox\hubspot\connections\ConnectionInterface;
use flipbox\hubspot\criteria\ContactListContactsAccessor;
use flipbox\hubspot\criteria\ContactListContactsMutator;
use flipbox\hubspot\criteria\ObjectAccessorInterface;
use flipbox\hubspot\criteria\ObjectMutatorInterface;
use flipbox\hubspot\helpers\CacheHelper;
use flipbox\hubspot\helpers\ConnectionHelper;
use flipbox\hubspot\helpers\TransformerHelper;
use flipbox\hubspot\HubSpot;
use flipbox\hubspot\pipeline\Resource;
use flipbox\hubspot\services\resources\traits\ReadObjectTrait;
use flipbox\hubspot\transformers\collections\DynamicTransformerCollection;
use flipbox\hubspot\transformers\collections\TransformerCollectionInterface;
use flipbox\hubspot\transformers\DynamicModelSuccess;
use Flipbox\Relay\Builder\RelayBuilderInterface;
use Flipbox\Relay\HubSpot\Builder\Resources\ContactList\Contacts\Add;
use Flipbox\Relay\HubSpot\Builder\Resources\ContactList\Contacts\All;
use Flipbox\Relay\HubSpot\Builder\Resources\ContactList\Contacts\Remove;
use League\Pipeline\PipelineBuilderInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\SimpleCache\CacheInterface;
use yii\base\Component;

/**
 * @author Flipbox Factory <hello@flipboxfactory.com>
 * @since 1.0.0
 */
class ContactListContacts extends Component
{
    use ReadObjectTrait;

    /**
     * The HubSpot Resource name
     */
    const HUBSPOT_RESOURCE = 'contactListContacts';

    /**
     * @inheritdoc
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
    public function getCriteria(array $config = []): ObjectAccessorInterface
    {
        return new ContactListContactsAccessor($config);
    }

    /**
     * @param array $config
     * @return ObjectMutatorInterface
     */
    public function getBuilder(array $config = []): ObjectMutatorInterface
    {
        return new ContactListContactsMutator($config);
    }

    /**
     * @inheritdoc
     */
    protected static function readRelayBuilderClass(): string
    {
        return All::class;
    }

    /**
     * @inheritdoc
     */
    protected static function addRelayBuilderClass(): string
    {
        return Add::class;
    }

    /**
     * @inheritdoc
     */
    protected static function removeRelayBuilderClass(): string
    {
        return Remove::class;
    }


    /*******************************************
     * ADD
     *******************************************/

    /**
     * @param ObjectMutatorInterface $criteria
     * @param null $source
     * @return mixed
     * @throws \yii\base\InvalidConfigException
     */
    public function add(
        ObjectMutatorInterface $criteria,
        $source = null
    ) {
        return $this->rawAdd(
            $criteria->getId(),
            $criteria->getPayload(),
            $criteria->getConnection(),
            $criteria->getCache(),
            $criteria->getTransformer(),
            $source
        );
    }

    /**
     * @param string $id
     * @param array $payload
     * @param ConnectionInterface|string|null $connection
     * @param CacheInterface|string|null $cache
     * @param TransformerCollectionInterface|array|null $transformer
     * @param null $source
     * @return mixed
     * @throws \yii\base\InvalidConfigException
     */
    public function rawAdd(
        string $id,
        array $payload,
        ConnectionInterface $connection = null,
        CacheInterface $cache = null,
        TransformerCollectionInterface $transformer = null,
        $source = null
    ) {
        return $this->rawAddPipeline(
            $id,
            $payload,
            $connection,
            $cache,
            $transformer
        )($source);
    }

    /**
     * @param ObjectMutatorInterface $criteria
     * @return PipelineBuilderInterface
     * @throws \yii\base\InvalidConfigException
     */
    public function addPipeline(
        ObjectMutatorInterface $criteria
    ): PipelineBuilderInterface {
        return $this->rawAddPipeline(
            $criteria->getId(),
            $criteria->getPayload(),
            $criteria->getConnection(),
            $criteria->getCache(),
            $criteria->getTransformer()
        );
    }

    /**
     * @param string $id
     * @param array $payload
     * @param ConnectionInterface|string|null $connection
     * @param CacheInterface|string|null $cache
     * @param TransformerCollectionInterface|array|null $transformer
     * @return PipelineBuilderInterface
     * @throws \yii\base\InvalidConfigException
     */
    public function rawAddPipeline(
        string $id,
        array $payload,
        ConnectionInterface $connection = null,
        CacheInterface $cache = null,
        TransformerCollectionInterface $transformer = null
    ): PipelineBuilderInterface {
        $transformer = TransformerHelper::populateTransformerCollection(
            TransformerHelper::resolveCollection($transformer),
            [
                'resource' => [static::addRelayBuilderClass()]
            ]
        );

        return (new Resource(
            $this->rawHttpAddRelay(
                $id,
                $payload,
                ConnectionHelper::resolveConnection($connection),
                CacheHelper::resolveCache($cache)
            ),
            $transformer,
            HubSpot::getInstance()->getPsrLogger()
        ));
    }

    /**
     * @param ObjectMutatorInterface $criteria
     * @return callable
     * @throws \yii\base\InvalidConfigException
     */
    public function httpAddRelay(
        ObjectMutatorInterface $criteria
    ): callable {
        return $this->rawHttpAddRelay(
            $criteria->getId(),
            $criteria->getPayload(),
            $criteria->getConnection(),
            $criteria->getCache()
        );
    }

    /**
     * @param string $id
     * @param array $payload
     * @param ConnectionInterface|string|null $connection
     * @param CacheInterface|string|null $cache
     * @return callable
     * @throws \yii\base\InvalidConfigException
     */
    public function rawHttpAddRelay(
        string $id,
        array $payload,
        ConnectionInterface $connection = null,
        CacheInterface $cache = null
    ): callable {
        $class = static::addRelayBuilderClass();

        /** @var RelayBuilderInterface $builder */
        $builder = new $class(
            $id,
            $payload,
            ConnectionHelper::resolveConnection($connection),
            CacheHelper::resolveCache($cache),
            HubSpot::getInstance()->getPsrLogger()
        );

        return $builder->build();
    }

    /**
     * @param ObjectMutatorInterface $criteria
     * @return ResponseInterface
     * @throws \yii\base\InvalidConfigException
     */
    public function httpAdd(
        ObjectMutatorInterface $criteria
    ): ResponseInterface {
        return $this->rawHttpAdd(
            $criteria->getId(),
            $criteria->getPayload(),
            $criteria->getConnection(),
            $criteria->getCache()
        );
    }

    /**
     * @param string $id
     * @param array $payload
     * @param ConnectionInterface|string|null $connection
     * @param CacheInterface|string|null $cache
     * @return ResponseInterface
     * @throws \yii\base\InvalidConfigException
     */
    public function rawHttpAdd(
        string $id,
        array $payload,
        ConnectionInterface $connection = null,
        CacheInterface $cache = null
    ): ResponseInterface {
        return $this->rawHttpAddRelay(
            $id,
            $payload,
            $connection,
            $cache
        )();
    }


    /*******************************************
     * REMOVE
     *******************************************/

    /**
     * @param ObjectMutatorInterface $criteria
     * @param null $source
     * @return mixed
     * @throws \yii\base\InvalidConfigException
     */
    public function remove(
        ObjectMutatorInterface $criteria,
        $source = null
    ) {
        return $this->rawRemove(
            $criteria->getId(),
            $criteria->getPayload(),
            $criteria->getConnection(),
            $criteria->getCache(),
            $criteria->getTransformer(),
            $source
        );
    }

    /**
     * @param string $id
     * @param array $payload
     * @param ConnectionInterface|string|null $connection
     * @param CacheInterface|string|null $cache
     * @param TransformerCollectionInterface|array|null $transformer
     * @param null $source
     * @return mixed
     * @throws \yii\base\InvalidConfigException
     */
    public function rawRemove(
        string $id,
        array $payload,
        ConnectionInterface $connection = null,
        CacheInterface $cache = null,
        TransformerCollectionInterface $transformer = null,
        $source = null
    ) {
        return $this->rawRemovePipeline(
            $id,
            $payload,
            $connection,
            $cache,
            $transformer
        )($source);
    }

    /**
     * @param ObjectMutatorInterface $criteria
     * @return PipelineBuilderInterface
     * @throws \yii\base\InvalidConfigException
     */
    public function removePipeline(
        ObjectMutatorInterface $criteria
    ): PipelineBuilderInterface {
        return $this->rawRemovePipeline(
            $criteria->getId(),
            $criteria->getPayload(),
            $criteria->getConnection(),
            $criteria->getCache(),
            $criteria->getTransformer()
        );
    }

    /**
     * @param string $id
     * @param array $payload
     * @param ConnectionInterface|string|null $connection
     * @param CacheInterface|string|null $cache
     * @param TransformerCollectionInterface|array|null $transformer
     * @return PipelineBuilderInterface
     * @throws \yii\base\InvalidConfigException
     */
    public function rawRemovePipeline(
        string $id,
        array $payload,
        ConnectionInterface $connection = null,
        CacheInterface $cache = null,
        TransformerCollectionInterface $transformer = null
    ): PipelineBuilderInterface {
        $transformer = TransformerHelper::populateTransformerCollection(
            TransformerHelper::resolveCollection($transformer),
            [
                'resource' => [static::removeRelayBuilderClass()]
            ]
        );

        return (new Resource(
            $this->rawHttpRemoveRelay(
                $id,
                $payload,
                ConnectionHelper::resolveConnection($connection),
                CacheHelper::resolveCache($cache)
            ),
            $transformer,
            HubSpot::getInstance()->getPsrLogger()
        ));
    }

    /**
     * @param ObjectMutatorInterface $criteria
     * @return callable
     * @throws \yii\base\InvalidConfigException
     */
    public function httpRemoveRelay(
        ObjectMutatorInterface $criteria
    ): callable {
        return $this->rawHttpRemoveRelay(
            $criteria->getId(),
            $criteria->getPayload(),
            $criteria->getConnection(),
            $criteria->getCache()
        );
    }

    /**
     * @param string $id
     * @param array $payload
     * @param ConnectionInterface|string|null $connection
     * @param CacheInterface|string|null $cache
     * @return callable
     * @throws \yii\base\InvalidConfigException
     */
    public function rawHttpRemoveRelay(
        string $id,
        array $payload,
        ConnectionInterface $connection = null,
        CacheInterface $cache = null
    ): callable {
        $class = static::removeRelayBuilderClass();

        /** @var RelayBuilderInterface $builder */
        $builder = new $class(
            $id,
            $payload,
            ConnectionHelper::resolveConnection($connection),
            CacheHelper::resolveCache($cache),
            HubSpot::getInstance()->getPsrLogger()
        );

        return $builder->build();
    }

    /**
     * @param ObjectMutatorInterface $criteria
     * @return ResponseInterface
     * @throws \yii\base\InvalidConfigException
     */
    public function httpRemove(
        ObjectMutatorInterface $criteria
    ): ResponseInterface {
        return $this->rawHttpRemove(
            $criteria->getId(),
            $criteria->getPayload(),
            $criteria->getConnection(),
            $criteria->getCache()
        );
    }

    /**
     * @param string $id
     * @param array $payload
     * @param ConnectionInterface|string|null $connection
     * @param CacheInterface|string|null $cache
     * @return ResponseInterface
     * @throws \yii\base\InvalidConfigException
     */
    public function rawHttpRemove(
        string $id,
        array $payload,
        ConnectionInterface $connection = null,
        CacheInterface $cache = null
    ): ResponseInterface {
        return $this->rawHttpRemoveRelay(
            $id,
            $payload,
            $connection,
            $cache
        )();
    }
}
