<?php

/**
 * @copyright  Copyright (c) Flipbox Digital Limited
 * @license    https://flipboxfactory.com/software/hubspot/license
 * @link       https://www.flipboxfactory.com/software/hubspot/
 */

namespace flipbox\hubspot\services\resources;

use flipbox\hubspot\connections\ConnectionInterface;
use flipbox\hubspot\criteria\IntegrationAccessorInterface;
use flipbox\hubspot\helpers\CacheHelper;
use flipbox\hubspot\helpers\ConnectionHelper;
use flipbox\hubspot\helpers\TransformerHelper;
use flipbox\hubspot\HubSpot;
use flipbox\hubspot\pipeline\Resource;
use flipbox\hubspot\transformers\collections\TransformerCollectionInterface;
use Flipbox\Relay\Builder\RelayBuilderInterface;
use Flipbox\Relay\HubSpot\Builder\Resources\Limit\Daily\Read;
use League\Pipeline\PipelineBuilderInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\SimpleCache\CacheInterface;
use yii\base\Component;

/**
 * @author Flipbox Factory <hello@flipboxfactory.com>
 * @since 1.0.0
 */
class Limit extends Component
{
    /**
     * The HubSpot Resource name
     */
    const HUBSPOT_RESOURCE = 'limit';

    /**
     * @return string
     */
    protected static function readDailyRelayBuilderClass(): string
    {
        return Read::class;
    }

    /**
     * @param ConnectionInterface|string|null $connection
     * @param CacheInterface|string|null $cache
     * @return callable
     * @throws \yii\base\InvalidConfigException
     */
    public function rawHttpReadDailyRelay(
        ConnectionInterface $connection = null,
        CacheInterface $cache = null
    ): callable {
        $class = static::readDailyRelayBuilderClass();

        /** @var RelayBuilderInterface $builder */
        $builder = new $class(
            ConnectionHelper::resolveConnection($connection),
            CacheHelper::resolveCache($cache),
            HubSpot::getInstance()->getPsrLogger()
        );

        return $builder->build();
    }

    /**
     * @param IntegrationAccessorInterface $criteria
     * @param null $source
     * @return mixed
     * @throws \yii\base\InvalidConfigException
     */
    public function readDaily(
        IntegrationAccessorInterface $criteria,
        $source = null
    ) {
        return $this->rawReadDaily(
            $criteria->getConnection(),
            $criteria->getCache(),
            $criteria->getTransformer(),
            $source
        );
    }

    /**
     * @param ConnectionInterface|string|null $connection
     * @param CacheInterface|string|null $cache
     * @param TransformerCollectionInterface|array|null $transformer
     * @param null $source
     * @return mixed
     * @throws \yii\base\InvalidConfigException
     */
    public function rawReadDaily(
        ConnectionInterface $connection = null,
        CacheInterface $cache = null,
        TransformerCollectionInterface $transformer = null,
        $source = null
    ) {
        return $this->rawReadDailyPipeline(
            $connection,
            $cache,
            $transformer
        )($source);
    }

    /**
     * @param IntegrationAccessorInterface $criteria
     * @return PipelineBuilderInterface
     * @throws \yii\base\InvalidConfigException
     */
    public function readDailyPipeline(
        IntegrationAccessorInterface $criteria
    ): PipelineBuilderInterface {
        return $this->rawReadDailyPipeline(
            $criteria->getConnection(),
            $criteria->getCache(),
            $criteria->getTransformer()
        );
    }

    /**
     * @param ConnectionInterface|string|null $connection
     * @param CacheInterface|string|null $cache
     * @param TransformerCollectionInterface|array|null $transformer
     * @return PipelineBuilderInterface
     * @throws \yii\base\InvalidConfigException
     */
    public function rawReadDailyPipeline(
        ConnectionInterface $connection = null,
        CacheInterface $cache = null,
        TransformerCollectionInterface $transformer = null
    ): PipelineBuilderInterface {
        $transformer = TransformerHelper::populateTransformerCollection(
            TransformerHelper::resolveCollection($transformer),
            [
                'resource' => [static::readDailyRelayBuilderClass()]
            ]
        );

        return (new Resource(
            $this->rawHttpReadDailyRelay(
                ConnectionHelper::resolveConnection($connection),
                $cache
            ),
            $transformer,
            HubSpot::getInstance()->getPsrLogger()
        ));
    }

    /**
     * @param IntegrationAccessorInterface $criteria
     * @return ResponseInterface
     * @throws \yii\base\InvalidConfigException
     */
    public function httpReadDaily(
        IntegrationAccessorInterface $criteria
    ): ResponseInterface {
        return $this->rawHttpReadDaily(
            $criteria->getConnection(),
            $criteria->getCache()
        )();
    }

    /**
     * @param ConnectionInterface|string|null $connection
     * @param CacheInterface|string|null $cache
     * @return ResponseInterface
     * @throws \yii\base\InvalidConfigException
     */
    public function rawHttpReadDaily(
        ConnectionInterface $connection = null,
        CacheInterface $cache = null
    ): ResponseInterface {
        return $this->rawHttpReadDailyRelay(
            $connection,
            $cache
        )();
    }

    /**
     * @param IntegrationAccessorInterface $criteria
     * @return callable
     * @throws \yii\base\InvalidConfigException
     */
    public function httpReadDailyRelay(
        IntegrationAccessorInterface $criteria
    ): callable {
        return $this->rawHttpReadDailyRelay(
            $criteria->getConnection(),
            $criteria->getCache()
        );
    }
}
