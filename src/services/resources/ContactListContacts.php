<?php

/**
 * @copyright  Copyright (c) Flipbox Digital Limited
 * @license    https://flipboxfactory.com/software/hubspot/license
 * @link       https://www.flipboxfactory.com/software/hubspot/
 */

namespace flipbox\hubspot\services\resources;

use flipbox\hubspot\builders\ContactListContactsBuilder;
use flipbox\hubspot\builders\ObjectBuilderInterface;
use flipbox\hubspot\connections\ConnectionInterface;
use flipbox\hubspot\criteria\ContactListContactsCriteria;
use flipbox\hubspot\criteria\ObjectCriteriaInterface;
use flipbox\hubspot\helpers\CacheHelper;
use flipbox\hubspot\helpers\ConnectionHelper;
use flipbox\hubspot\helpers\TransformerHelper;
use flipbox\hubspot\HubSpot;
use flipbox\hubspot\pipeline\Resource;
use flipbox\hubspot\services\resources\traits\ReadObjectTrait;
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
     * @return ObjectCriteriaInterface
     */
    public function getCriteria(array $config = []): ObjectCriteriaInterface
    {
        return new ContactListContactsCriteria($config);
    }

    /**
     * @param array $config
     * @return ObjectBuilderInterface
     */
    public function getBuilder(array $config = []): ObjectBuilderInterface
    {
        return new ContactListContactsBuilder($config);
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
     * @param ObjectBuilderInterface $builder
     * @param ConnectionInterface|string|null $connection
     * @param CacheInterface|string|null $cache
     * @param TransformerCollectionInterface|array|null $transformer
     * @param null $source
     * @return mixed
     * @throws \yii\base\InvalidConfigException
     */
    public function add(
        ObjectBuilderInterface $builder,
        ConnectionInterface $connection = null,
        CacheInterface $cache = null,
        TransformerCollectionInterface $transformer = null,
        $source = null
    ) {
        return $this->rawAdd(
            $builder->getId(),
            $builder->getPayload(),
            $connection,
            $cache,
            $transformer,
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
     * @param ObjectBuilderInterface $builder
     * @param ConnectionInterface|string|null $connection
     * @param CacheInterface|string|null $cache
     * @param TransformerCollectionInterface|array|null $transformer
     * @return PipelineBuilderInterface
     * @throws \yii\base\InvalidConfigException
     */
    public function addPipeline(
        ObjectBuilderInterface $builder,
        ConnectionInterface $connection = null,
        CacheInterface $cache = null,
        TransformerCollectionInterface $transformer = null
    ): PipelineBuilderInterface {
        return $this->rawAddPipeline(
            $builder->getId(),
            $builder->getPayload(),
            $connection,
            $cache,
            $transformer
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
     * @param ObjectBuilderInterface $builder
     * @param ConnectionInterface|string|null $connection
     * @param CacheInterface|string|null $cache
     * @return callable
     * @throws \yii\base\InvalidConfigException
     */
    public function httpAddRelay(
        ObjectBuilderInterface $builder,
        ConnectionInterface $connection = null,
        CacheInterface $cache = null
    ): callable {
        return $this->rawHttpAddRelay(
            $builder->getId(),
            $builder->getPayload(),
            $connection,
            $cache
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
     * @param ObjectBuilderInterface $builder
     * @param ConnectionInterface|string|null $connection
     * @param CacheInterface|string|null $cache
     * @return ResponseInterface
     * @throws \yii\base\InvalidConfigException
     */
    public function httpAdd(
        ObjectBuilderInterface $builder,
        ConnectionInterface $connection = null,
        CacheInterface $cache = null
    ): ResponseInterface {
        return $this->rawHttpAdd(
            $builder->getId(),
            $builder->getPayload(),
            $connection,
            $cache
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
     * @param ObjectBuilderInterface $builder
     * @param ConnectionInterface|string|null $connection
     * @param CacheInterface|string|null $cache
     * @param TransformerCollectionInterface|array|null $transformer
     * @param null $source
     * @return mixed
     * @throws \yii\base\InvalidConfigException
     */
    public function remove(
        ObjectBuilderInterface $builder,
        ConnectionInterface $connection = null,
        CacheInterface $cache = null,
        TransformerCollectionInterface $transformer = null,
        $source = null
    ) {
        return $this->rawRemove(
            $builder->getId(),
            $builder->getPayload(),
            $connection,
            $cache,
            $transformer,
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
     * @param ObjectBuilderInterface $builder
     * @param ConnectionInterface|string|null $connection
     * @param CacheInterface|string|null $cache
     * @param TransformerCollectionInterface|array|null $transformer
     * @return PipelineBuilderInterface
     * @throws \yii\base\InvalidConfigException
     */
    public function removePipeline(
        ObjectBuilderInterface $builder,
        ConnectionInterface $connection = null,
        CacheInterface $cache = null,
        TransformerCollectionInterface $transformer = null
    ): PipelineBuilderInterface {
        return $this->rawRemovePipeline(
            $builder->getId(),
            $builder->getPayload(),
            $connection,
            $cache,
            $transformer
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
                CacheHelper::$cache($connection)
            ),
            $transformer,
            HubSpot::getInstance()->getPsrLogger()
        ));
    }

    /**
     * @param ObjectBuilderInterface $builder
     * @param ConnectionInterface|string|null $connection
     * @param CacheInterface|string|null $cache
     * @return callable
     * @throws \yii\base\InvalidConfigException
     */
    public function httpRemoveRelay(
        ObjectBuilderInterface $builder,
        ConnectionInterface $connection = null,
        CacheInterface $cache = null
    ): callable {
        return $this->rawHttpRemoveRelay(
            $builder->getId(),
            $builder->getPayload(),
            $connection,
            $cache
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
     * @param ObjectBuilderInterface $builder
     * @param ConnectionInterface|string|null $connection
     * @param CacheInterface|string|null $cache
     * @return ResponseInterface
     * @throws \yii\base\InvalidConfigException
     */
    public function httpRemove(
        ObjectBuilderInterface $builder,
        ConnectionInterface $connection = null,
        CacheInterface $cache = null
    ): ResponseInterface {
        return $this->rawHttpRemove(
            $builder->getId(),
            $builder->getPayload(),
            $connection,
            $cache
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
