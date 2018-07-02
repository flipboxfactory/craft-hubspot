<?php

/**
 * @copyright  Copyright (c) Flipbox Digital Limited
 * @license    https://flipboxfactory.com/software/hubspot/license
 * @link       https://www.flipboxfactory.com/software/hubspot/
 */

namespace flipbox\hubspot\services\resources;

use Craft;
use flipbox\hubspot\builders\TimelineEventBuilderInterface;
use flipbox\hubspot\connections\IntegrationConnectionInterface;
use flipbox\hubspot\criteria\TimelineEventCriteriaInterface;
use flipbox\hubspot\helpers\CacheHelper;
use flipbox\hubspot\helpers\ConnectionHelper;
use flipbox\hubspot\helpers\TransformerHelper;
use flipbox\hubspot\HubSpot;
use flipbox\hubspot\pipeline\Resource;
use flipbox\hubspot\queue\jobs\UpsertTimelineEvent;
use flipbox\hubspot\transformers\collections\TransformerCollectionInterface;
use Flipbox\Relay\HubSpot\Builder\Resources\Timeline\Event\Read;
use Flipbox\Relay\HubSpot\Builder\Resources\Timeline\Event\Upsert;
use League\Pipeline\PipelineBuilderInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\SimpleCache\CacheInterface;
use yii\base\Component;

/**
 * @author Flipbox Factory <hello@flipboxfactory.com>
 * @since 1.0.0
 */
class TimelineEvents extends Component
{
    /**
     * The HubSpot Resource name
     */
    const HUBSPOT_RESOURCE = 'timelineEvents';

    /*******************************************
     * ELEMENT SYNC JOBS
     *******************************************/

    /**
     * @param TimelineEventBuilderInterface $builder
     * @param IntegrationConnectionInterface $connection
     * @return null|string
     * @throws \yii\base\InvalidConfigException
     */
    public function upsertJob(
        TimelineEventBuilderInterface $builder,
        IntegrationConnectionInterface $connection
    ) {
        return $this->rawUpsertJob(
            $builder->getId(),
            $builder->getTypeId(),
            $builder->getPayload(),
            $connection
        );
    }

    /**
     * @param string $id
     * @param string $typeId
     * @param array $payload
     * @param IntegrationConnectionInterface|null $connection
     * @return null|string
     * @throws \yii\base\InvalidConfigException
     */
    public function rawUpsertJob(
        string $id,
        string $typeId,
        array $payload,
        IntegrationConnectionInterface $connection = null
    ) {
        return Craft::$app->getQueue()->push(new UpsertTimelineEvent([
            'id' => $id,
            'typeId' => $typeId,
            'payload' => $payload,
            'connection' => ConnectionHelper::resolveIntegrationConnection($connection)
        ]));
    }

    /*******************************************
     * GET / READ
     *******************************************/

    /**
     * @param TimelineEventCriteriaInterface $criteria
     * @param null $source
     * @return mixed
     * @throws \yii\base\InvalidConfigException
     */
    public function read(
        TimelineEventCriteriaInterface $criteria,
        $source = null
    ) {
        return $this->rawRead(
            $criteria->getId(),
            $criteria->getTypeId(),
            $criteria->getConnection(),
            $criteria->getCache(),
            $criteria->getTransformer(),
            $source
        );
    }

    /**
     * @param string $id
     * @param string $typeId
     * @param IntegrationConnectionInterface|null $connection
     * @param CacheInterface|string|null $cache
     * @param TransformerCollectionInterface|array|null $transformer
     * @param null $source
     * @return mixed
     * @throws \yii\base\InvalidConfigException
     */
    public function rawRead(
        string $id,
        string $typeId,
        IntegrationConnectionInterface $connection = null,
        CacheInterface $cache = null,
        TransformerCollectionInterface $transformer = null,
        $source = null
    ) {
        return $this->rawReadPipeline(
            $id,
            $typeId,
            $connection,
            $cache,
            $transformer
        )($source);
    }

    /**
     * @param TimelineEventCriteriaInterface $criteria
     * @return PipelineBuilderInterface
     * @throws \yii\base\InvalidConfigException
     */
    public function readPipeline(
        TimelineEventCriteriaInterface $criteria
    ): PipelineBuilderInterface {
        return $this->rawReadPipeline(
            $criteria->getId(),
            $criteria->getTypeId(),
            $criteria->getConnection(),
            $criteria->getCache(),
            $criteria->getTransformer()
        );
    }

    /**
     * @param string $id
     * @param string $typeId
     * @param IntegrationConnectionInterface|null $connection
     * @param CacheInterface|string|null $cache
     * @param TransformerCollectionInterface|array|null $transformer
     * @return PipelineBuilderInterface
     * @throws \yii\base\InvalidConfigException
     */
    public function rawReadPipeline(
        string $id,
        string $typeId,
        IntegrationConnectionInterface $connection = null,
        CacheInterface $cache = null,
        TransformerCollectionInterface $transformer = null
    ): PipelineBuilderInterface {
        $transformer = TransformerHelper::populateTransformerCollection(
            TransformerHelper::resolveCollection($transformer),
            [
                'resource' => [Read::class]
            ]
        );

        return (new Resource(
            $this->rawHttpReadRelay(
                $id,
                $typeId,
                $connection,
                $cache
            ),
            $transformer,
            HubSpot::getInstance()->getPsrLogger()
        ));
    }

    /**
     * @param TimelineEventCriteriaInterface $criteria
     * @return ResponseInterface
     * @throws \yii\base\InvalidConfigException
     */
    public function httpRead(
        TimelineEventCriteriaInterface $criteria
    ): ResponseInterface {
        return $this->rawHttpRead(
            $criteria->getId(),
            $criteria->getTypeId(),
            $criteria->getConnection(),
            $criteria->getCache()
        );
    }

    /**
     * @param string $id
     * @param string $typeId
     * @param IntegrationConnectionInterface|null $connection
     * @param CacheInterface|string|null $cache
     * @return ResponseInterface
     * @throws \yii\base\InvalidConfigException
     */
    public function rawHttpRead(
        string $id,
        string $typeId,
        IntegrationConnectionInterface $connection = null,
        CacheInterface $cache = null
    ): ResponseInterface {
        return $this->rawHttpReadRelay(
            $id,
            $typeId,
            $connection,
            $cache
        )();
    }

    /**
     * @param TimelineEventCriteriaInterface $criteria
     * @return callable
     * @throws \yii\base\InvalidConfigException
     */
    public function httpReadRelay(
        TimelineEventCriteriaInterface $criteria
    ): callable {
        return $this->rawHttpReadRelay(
            $criteria->getId(),
            $criteria->getTypeId(),
            $criteria->getConnection(),
            $criteria->getCache()
        );
    }

    /**
     * @param string $id
     * @param string $typeId
     * @param IntegrationConnectionInterface|null $connection
     * @param CacheInterface|string|null $cache
     * @return callable
     * @throws \yii\base\InvalidConfigException
     */
    public function rawHttpReadRelay(
        string $id,
        string $typeId,
        IntegrationConnectionInterface $connection = null,
        CacheInterface $cache = null
    ): callable {
        $connection = ConnectionHelper::resolveIntegrationConnection($connection);

        return (new Read(
            $connection->getAppId(),
            $id,
            $typeId,
            $connection,
            CacheHelper::resolveCache($cache),
            HubSpot::getInstance()->getPsrLogger()
        ))->build();
    }


    /*******************************************
     * UPSERT
     *******************************************/

    /**
     * @param TimelineEventBuilderInterface $builder
     * @param IntegrationConnectionInterface $connection
     * @param CacheInterface $cache
     * @param TransformerCollectionInterface|array|null $transformer
     * @param mixed|null $source
     * @return mixed
     * @throws \yii\base\InvalidConfigException
     */
    public function upsert(
        TimelineEventBuilderInterface $builder,
        IntegrationConnectionInterface $connection = null,
        CacheInterface $cache = null,
        TransformerCollectionInterface $transformer = null,
        $source = null
    ) {
        return $this->rawUpsert(
            $builder->getTypeId(),
            $builder->getId(),
            $builder->getPayload(),
            $connection,
            $cache,
            $transformer,
            $source
        );
    }

    /**
     * @param string $typeId
     * @param string $id
     * @param array $payload
     * @param IntegrationConnectionInterface|null $connection
     * @param CacheInterface|string|null $cache
     * @param TransformerCollectionInterface|array|null $transformer
     * @param null $source
     * @return mixed
     * @throws \yii\base\InvalidConfigException
     */
    public function rawUpsert(
        string $typeId,
        string $id,
        array $payload,
        IntegrationConnectionInterface $connection = null,
        CacheInterface $cache = null,
        TransformerCollectionInterface $transformer = null,
        $source = null
    ) {
        return $this->rawUpsertPipeline(
            $typeId,
            $id,
            $payload,
            $connection,
            $cache,
            $transformer
        )($source);
    }

    /**
     * @param TimelineEventBuilderInterface $builder
     * @param IntegrationConnectionInterface|null $connection
     * @param CacheInterface|string|null $cache
     * @param TransformerCollectionInterface|array|null $transformer
     * @return PipelineBuilderInterface
     * @throws \yii\base\InvalidConfigException
     */
    public function upsertPipeline(
        TimelineEventBuilderInterface $builder,
        IntegrationConnectionInterface $connection = null,
        CacheInterface $cache = null,
        TransformerCollectionInterface $transformer = null
    ): PipelineBuilderInterface {
        return $this->rawUpsertPipeline(
            $builder->getTypeId(),
            $builder->getId(),
            $builder->getPayload(),
            $connection,
            $cache,
            $transformer
        );
    }

    /**
     * @param string $typeId
     * @param string $id
     * @param array $payload
     * @param IntegrationConnectionInterface|null $connection
     * @param CacheInterface|string|null $cache
     * @param TransformerCollectionInterface|array|null $transformer
     * @return PipelineBuilderInterface
     * @throws \yii\base\InvalidConfigException
     */
    public function rawUpsertPipeline(
        string $typeId,
        string $id,
        array $payload,
        IntegrationConnectionInterface $connection = null,
        CacheInterface $cache = null,
        TransformerCollectionInterface $transformer = null
    ): PipelineBuilderInterface {
        $transformer = TransformerHelper::populateTransformerCollection(
            TransformerHelper::resolveCollection($transformer),
            [
                'resource' => [Upsert::class]
            ]
        );

        return (new Resource(
            $this->rawHttpUpsertRelay(
                $typeId,
                $id,
                $payload,
                ConnectionHelper::resolveIntegrationConnection($connection),
                $cache
            ),
            $transformer,
            HubSpot::getInstance()->getPsrLogger()
        ));
    }

    /**
     * @param TimelineEventBuilderInterface $builder
     * @param IntegrationConnectionInterface|null $connection
     * @param CacheInterface|string|null $cache
     * @return ResponseInterface
     * @throws \yii\base\InvalidConfigException
     */
    public function httpUpsert(
        TimelineEventBuilderInterface $builder,
        IntegrationConnectionInterface $connection = null,
        CacheInterface $cache = null
    ): ResponseInterface {
        return $this->rawHttpUpsert(
            $builder->getTypeId(),
            $builder->getId(),
            $builder->getPayload(),
            $connection,
            $cache
        );
    }

    /**
     * @param string $typeId
     * @param string $id
     * @param array $payload
     * @param IntegrationConnectionInterface|null $connection
     * @param CacheInterface|string|null $cache
     * @return ResponseInterface
     * @throws \yii\base\InvalidConfigException
     */
    public function rawHttpUpsert(
        string $typeId,
        string $id,
        array $payload,
        IntegrationConnectionInterface $connection = null,
        CacheInterface $cache = null
    ): ResponseInterface {
        return $this->rawHttpUpsertRelay(
            $typeId,
            $id,
            $payload,
            $connection,
            $cache
        )();
    }

    /**
     * @param TimelineEventBuilderInterface $builder
     * @param IntegrationConnectionInterface|null $connection
     * @param CacheInterface|string|null $cache
     * @return callable
     * @throws \yii\base\InvalidConfigException
     */
    public function httpUpsertRelay(
        TimelineEventBuilderInterface $builder,
        IntegrationConnectionInterface $connection = null,
        CacheInterface $cache = null
    ): callable {
        return $this->rawHttpUpsertRelay(
            $builder->getTypeId(),
            $builder->getId(),
            $builder->getPayload(),
            $connection,
            $cache
        );
    }

    /**
     * @param string $typeId
     * @param string $id
     * @param array $payload
     * @param IntegrationConnectionInterface|null $connection
     * @param CacheInterface|string|null $cache
     * @return callable
     * @throws \yii\base\InvalidConfigException
     */
    public function rawHttpUpsertRelay(
        string $typeId,
        string $id,
        array $payload,
        IntegrationConnectionInterface $connection = null,
        CacheInterface $cache = null
    ): callable {
        $connection = ConnectionHelper::resolveIntegrationConnection($connection);

        return (new Upsert(
            $connection->getAppId(),
            $typeId,
            $id,
            $payload,
            $connection,
            CacheHelper::resolveCache($cache),
            HubSpot::getInstance()->getPsrLogger()
        ))->build();
    }
}
