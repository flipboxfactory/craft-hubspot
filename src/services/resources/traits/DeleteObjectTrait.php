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
trait DeleteObjectTrait
{
    /**
     * @return string
     */
    protected abstract static function deleteRelayBuilderClass(): string;

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
    public function delete(
        ObjectMutatorInterface $criteria,
        array $extra = []
    ) {
        return $this->rawDelete(
            $criteria->getId(),
            $criteria->getConnection(),
            $criteria->getCache(),
            $criteria->getTransformer(),
            $extra
        );
    }

    /**
     * @param string $id
     * @param ConnectionInterface|string|null $connection
     * @param CacheInterface|string|null $cache
     * @param TransformerCollectionInterface|array|null $transformer
     * @param array $extra
     * @return mixed
     * @throws \yii\base\InvalidConfigException
     */
    public function rawDelete(
        string $id,
        ConnectionInterface $connection = null,
        CacheInterface $cache = null,
        TransformerCollectionInterface $transformer = null,
        array $extra = []
    ) {
        return $this->rawDeletePipeline(
            $id,
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
    public function deletePipeline(
        ObjectMutatorInterface $criteria
    ): PipelineBuilderInterface {
        return $this->rawDeletePipeline(
            $criteria->getId(),
            $criteria->getConnection(),
            $criteria->getCache(),
            $criteria->getTransformer()
        );
    }

    /**
     * @param string $id
     * @param ConnectionInterface|string|null $connection
     * @param CacheInterface|string|null $cache
     * @param TransformerCollectionInterface|array|null $transformer
     * @return PipelineBuilderInterface
     * @throws \yii\base\InvalidConfigException
     */
    public function rawDeletePipeline(
        string $id,
        ConnectionInterface $connection = null,
        CacheInterface $cache = null,
        TransformerCollectionInterface $transformer = null
    ): PipelineBuilderInterface {
        $transformer = TransformerHelper::populateTransformerCollection(
            TransformerHelper::resolveCollection($transformer, static::defaultTransformer()),
            [
                'resource' => [static::deleteRelayBuilderClass()]
            ]
        );

        return (new Resource(
            $this->rawHttpDeleteRelay(
                $id,
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
    public function httpDeleteRelay(
        ObjectMutatorInterface $criteria
    ): callable {
        return $this->rawHttpDeleteRelay(
            $criteria->getId(),
            $criteria->getConnection(),
            $criteria->getCache()
        );
    }

    /**
     * @param string $id
     * @param ConnectionInterface|string|null $connection
     * @param CacheInterface|string|null $cache
     * @return callable
     * @throws \yii\base\InvalidConfigException
     */
    public function rawHttpDeleteRelay(
        string $id,
        ConnectionInterface $connection = null,
        CacheInterface $cache = null
    ): callable {
        $class = static::deleteRelayBuilderClass();

        /** @var RelayBuilderInterface $criteria */
        $criteria = new $class(
            $id,
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
    public function httpDelete(
        ObjectMutatorInterface $criteria
    ): ResponseInterface {
        return $this->rawHttpDelete(
            $criteria->getId(),
            $criteria->getConnection(),
            $criteria->getCache()
        );
    }

    /**
     * @param string $id
     * @param ConnectionInterface|string|null $connection
     * @param CacheInterface|string|null $cache
     * @return ResponseInterface
     * @throws \yii\base\InvalidConfigException
     */
    public function rawHttpDelete(
        string $id,
        ConnectionInterface $connection = null,
        CacheInterface $cache = null
    ): ResponseInterface {
        return $this->rawHttpDeleteRelay(
            $id,
            $connection,
            $cache
        )();
    }
}
