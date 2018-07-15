<?php

/**
 * @copyright  Copyright (c) Flipbox Digital Limited
 * @license    https://flipboxfactory.com/software/hubspot/license
 * @link       https://www.flipboxfactory.com/software/hubspot/
 */

namespace flipbox\hubspot\services\resources\traits;

use flipbox\hubspot\connections\ConnectionInterface;
use flipbox\hubspot\criteria\ObjectMutatorInterface;
use flipbox\hubspot\helpers\CacheHelper;
use flipbox\hubspot\helpers\ConnectionHelper;
use flipbox\hubspot\helpers\TransformerHelper;
use flipbox\hubspot\HubSpot;
use flipbox\hubspot\pipeline\Resource;
use flipbox\hubspot\transformers\collections\TransformerCollectionInterface;
use Flipbox\Relay\Builder\RelayBuilderInterface;
use League\Pipeline\PipelineBuilderInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\SimpleCache\CacheInterface;

/**
 * @author Flipbox Factory <hello@flipboxfactory.com>
 * @since 1.0.0
 */
trait UpdateObjectTrait
{
    /**
     * @return string
     */
    protected abstract static function updateRelayBuilderClass(): string;

    /**
     * @return array|TransformerCollectionInterface
     */
    public abstract static function defaultTransformer();

    /**
     * @param ObjectMutatorInterface $criteria
     * @param array $extra
     * @return mixed
     * @throws \yii\base\InvalidConfigException
     */
    public function update(
        ObjectMutatorInterface $criteria,
        array $extra = []
    ) {
        return $this->rawUpdate(
            $criteria->getId(),
            $criteria->getPayload(),
            $criteria->getConnection(),
            $criteria->getCache(),
            $criteria->getTransformer(),
            $extra
        );
    }

    /**
     * @param string $id
     * @param array $payload
     * @param ConnectionInterface|string|null $connection
     * @param CacheInterface|string|null $cache
     * @param TransformerCollectionInterface|array|null $transformer
     * @param array $extra
     * @return mixed
     * @throws \yii\base\InvalidConfigException
     */
    public function rawUpdate(
        string $id,
        array $payload,
        ConnectionInterface $connection = null,
        CacheInterface $cache = null,
        TransformerCollectionInterface $transformer = null,
        array $extra = []
    ) {
        return $this->rawUpdatePipeline(
            $id,
            $payload,
            $connection,
            $cache,
            $transformer
        )($extra);
    }

    /**
     * @param ObjectMutatorInterface $criteria
     * @return PipelineBuilderInterface
     * @throws \yii\base\InvalidConfigException
     */
    public function updatePipeline(
        ObjectMutatorInterface $criteria
    ): PipelineBuilderInterface {
        return $this->rawUpdatePipeline(
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
    public function rawUpdatePipeline(
        string $id,
        array $payload,
        ConnectionInterface $connection = null,
        CacheInterface $cache = null,
        TransformerCollectionInterface $transformer = null
    ): PipelineBuilderInterface {
        $transformer = TransformerHelper::populateTransformerCollection(
            TransformerHelper::resolveCollection($transformer, static::defaultTransformer()),
            [
                'resource' => [static::updateRelayBuilderClass()]
            ]
        );

        return (new Resource(
            $this->rawHttpUpdateRelay(
                $id,
                $payload,
                ConnectionHelper::resolveConnection($connection),
                $cache
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
    public function httpUpdateRelay(
        ObjectMutatorInterface $criteria
    ): callable {
        return $this->rawHttpUpdateRelay(
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
    public function rawHttpUpdateRelay(
        string $id,
        array $payload,
        ConnectionInterface $connection = null,
        CacheInterface $cache = null
    ): callable {
        $class = static::updateRelayBuilderClass();

        /** @var RelayBuilderInterface $criteria */
        $criteria = new $class(
            $id,
            $payload,
            ConnectionHelper::resolveConnection($connection),
            CacheHelper::resolveCache($cache),
            HubSpot::getInstance()->getPsrLogger()
        );

        return $criteria->build();
    }

    /**
     * @param ObjectMutatorInterface $criteria
     * @return ResponseInterface
     * @throws \yii\base\InvalidConfigException
     */
    public function httpUpdate(
        ObjectMutatorInterface $criteria
    ): ResponseInterface {
        return $this->rawHttpUpdate(
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
    public function rawHttpUpdate(
        string $id,
        array $payload,
        ConnectionInterface $connection = null,
        CacheInterface $cache = null
    ): ResponseInterface {
        return $this->rawHttpUpdateRelay(
            $id,
            $payload,
            $connection,
            $cache
        )();
    }
}
