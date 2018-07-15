<?php

/**
 * @copyright  Copyright (c) Flipbox Digital Limited
 * @license    https://flipboxfactory.com/software/hubspot/license
 * @link       https://www.flipboxfactory.com/software/hubspot/
 */

namespace flipbox\hubspot\services\resources\traits;

use flipbox\hubspot\connections\ConnectionInterface;
use flipbox\hubspot\criteria\ObjectAccessorInterface;
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
trait ReadObjectTrait
{
    /**
     * @return string
     */
    protected abstract static function readRelayBuilderClass(): string;

    /**
     * @return array|TransformerCollectionInterface
     */
    public abstract static function defaultTransformer();

    /**
     * @param ObjectAccessorInterface $criteria
     * @param array $extra
     * @return mixed
     * @throws \yii\base\InvalidConfigException
     */
    public function read(
        ObjectAccessorInterface $criteria,
        array $extra = []
    ) {
        return $this->rawRead(
            $criteria->getId(),
            $criteria->getConnection(),
            $criteria->getCache(),
            $criteria->getTransformer(),
            $extra
        );
    }

    /**
     * @param $id
     * @param ConnectionInterface|string|null $connection
     * @param CacheInterface|string|null $cache
     * @param TransformerCollectionInterface|array|null $transformer
     * @param array $extra
     * @return mixed
     * @throws \yii\base\InvalidConfigException
     */
    public function rawRead(
        string $id,
        ConnectionInterface $connection = null,
        CacheInterface $cache = null,
        TransformerCollectionInterface $transformer = null,
        array $extra = []
    ) {
        return $this->rawReadPipeline(
            $id,
            $connection,
            $cache,
            $transformer
        )($extra);
    }

    /**
     * @param ObjectAccessorInterface $criteria
     * @return PipelineBuilderInterface
     * @throws \yii\base\InvalidConfigException
     */
    public function readPipeline(
        ObjectAccessorInterface $criteria
    ): PipelineBuilderInterface {
        return $this->rawReadPipeline(
            $criteria->getId(),
            $criteria->getConnection(),
            $criteria->getCache(),
            $criteria->getTransformer()
        );
    }

    /**
     * @param $id
     * @param ConnectionInterface|string|null $connection
     * @param CacheInterface|string|null $cache
     * @param TransformerCollectionInterface|array|null $transformer
     * @return PipelineBuilderInterface
     * @throws \yii\base\InvalidConfigException
     */
    public function rawReadPipeline(
        string $id,
        ConnectionInterface $connection = null,
        CacheInterface $cache = null,
        TransformerCollectionInterface $transformer = null
    ): PipelineBuilderInterface {
        $transformer = TransformerHelper::populateTransformerCollection(
            TransformerHelper::resolveCollection($transformer, static::defaultTransformer()),
            [
                'resource' => [static::readRelayBuilderClass()]
            ]
        );

        return (new Resource(
            $this->rawHttpReadRelay(
                $id,
                ConnectionHelper::resolveConnection($connection),
                $cache
            ),
            $transformer,
            HubSpot::getInstance()->getPsrLogger()
        ));
    }

    /**
     * @param ObjectAccessorInterface $criteria
     * @return ResponseInterface
     * @throws \yii\base\InvalidConfigException
     */
    public function httpRead(
        ObjectAccessorInterface $criteria
    ): ResponseInterface {
        return $this->rawHttpRead(
            $criteria->getId(),
            $criteria->getConnection(),
            $criteria->getCache()
        )();
    }

    /**
     * @param $id
     * @param ConnectionInterface|string|null $connection
     * @param CacheInterface|string|null $cache
     * @return ResponseInterface
     * @throws \yii\base\InvalidConfigException
     */
    public function rawHttpRead(
        $id,
        ConnectionInterface $connection = null,
        CacheInterface $cache = null
    ): ResponseInterface {
        return $this->rawHttpReadRelay(
            $id,
            $connection,
            $cache
        )();
    }

    /**
     * @param ObjectAccessorInterface $criteria
     * @return callable
     * @throws \yii\base\InvalidConfigException
     */
    public function httpReadRelay(
        ObjectAccessorInterface $criteria
    ): callable {
        return $this->rawHttpReadRelay(
            $criteria->getId(),
            $criteria->getConnection(),
            $criteria->getCache()
        );
    }

    /**
     * @param $id
     * @param ConnectionInterface|string|null $connection
     * @param CacheInterface|string|null $cache
     * @return callable
     * @throws \yii\base\InvalidConfigException
     */
    public function rawHttpReadRelay(
        string $id,
        ConnectionInterface $connection = null,
        CacheInterface $cache = null
    ): callable {
        $class = static::readRelayBuilderClass();

        /** @var RelayBuilderInterface $criteria */
        $criteria = new $class(
            $id,
            ConnectionHelper::resolveConnection($connection),
            CacheHelper::resolveCache($cache),
            HubSpot::getInstance()->getPsrLogger()
        );

        return $criteria->build();
    }
}
