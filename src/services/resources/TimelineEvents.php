<?php

/**
 * @copyright  Copyright (c) Flipbox Digital Limited
 * @license    https://flipboxfactory.com/software/hubspot/license
 * @link       https://www.flipboxfactory.com/software/hubspot/
 */

namespace flipbox\hubspot\services\resources;

use Craft;
use flipbox\hubspot\connections\IntegrationConnectionInterface;
use flipbox\hubspot\criteria\TimelineEventAccessorInterface;
use flipbox\hubspot\criteria\TimelineEventMutatorInterface;
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
     * @param TimelineEventMutatorInterface $criteria
     * @return null|string
     * @throws \yii\base\InvalidConfigException
     */
    public function upsertJob(
        TimelineEventMutatorInterface $criteria
    ) {
        return $this->rawUpsertJob(
            $criteria->getId(),
            $criteria->getTypeId(),
            $criteria->getPayload(),
            $criteria->getConnection()
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
     * @param TimelineEventAccessorInterface $criteria
     * @param array $extra
     * @return mixed
     * @throws \yii\base\InvalidConfigException
     */
    public function read(
        TimelineEventAccessorInterface $criteria,
        array $extra = []
    ) {
        return $this->rawRead(
            $criteria->getId(),
            $criteria->getTypeId(),
            $criteria->getConnection(),
            $criteria->getCache(),
            $criteria->getTransformer(),
            $extra
        );
    }

    /**
     * @param string $id
     * @param string $typeId
     * @param IntegrationConnectionInterface|null $connection
     * @param CacheInterface|string|null $cache
     * @param TransformerCollectionInterface|array|null $transformer
     * @param array $extra
     * @return mixed
     * @throws \yii\base\InvalidConfigException
     */
    public function rawRead(
        string $id,
        string $typeId,
        IntegrationConnectionInterface $connection = null,
        CacheInterface $cache = null,
        TransformerCollectionInterface $transformer = null,
        array $extra = []
    ) {
        return $this->rawReadPipeline(
            $id,
            $typeId,
            $connection,
            $cache,
            $transformer
        )($extra);
    }

    /**
     * @param TimelineEventAccessorInterface $criteria
     * @return PipelineBuilderInterface
     * @throws \yii\base\InvalidConfigException
     */
    public function readPipeline(
        TimelineEventAccessorInterface $criteria
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
     * @param TimelineEventAccessorInterface $criteria
     * @return ResponseInterface
     * @throws \yii\base\InvalidConfigException
     */
    public function httpRead(
        TimelineEventAccessorInterface $criteria
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
     * @param TimelineEventAccessorInterface $criteria
     * @return callable
     * @throws \yii\base\InvalidConfigException
     */
    public function httpReadRelay(
        TimelineEventAccessorInterface $criteria
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
     * @param TimelineEventMutatorInterface $criteria
     * @param array $extra
     * @return mixed
     * @throws \yii\base\InvalidConfigException
     */
    public function upsert(
        TimelineEventMutatorInterface $criteria,
        array $extra = []
    ) {
        return $this->rawUpsert(
            $criteria->getTypeId(),
            $criteria->getId(),
            $criteria->getPayload(),
            $criteria->getConnection(),
            $criteria->getCache(),
            $criteria->getTransformer(),
            $extra
        );
    }

    /**
     * @param string $typeId
     * @param string $id
     * @param array $payload
     * @param IntegrationConnectionInterface|null $connection
     * @param CacheInterface|string|null $cache
     * @param TransformerCollectionInterface|array|null $transformer
     * @param array $extra
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
        array $extra = []
    ) {
        return $this->rawUpsertPipeline(
            $typeId,
            $id,
            $payload,
            $connection,
            $cache,
            $transformer
        )($extra);
    }

    /**
     * @param TimelineEventMutatorInterface $criteria
     * @return PipelineBuilderInterface
     * @throws \yii\base\InvalidConfigException
     */
    public function upsertPipeline(
        TimelineEventMutatorInterface $criteria
    ): PipelineBuilderInterface {
        return $this->rawUpsertPipeline(
            $criteria->getTypeId(),
            $criteria->getId(),
            $criteria->getPayload(),
            $criteria->getConnection(),
            $criteria->getCache(),
            $criteria->getTransformer()
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
     * @param TimelineEventMutatorInterface $criteria
     * @return ResponseInterface
     * @throws \yii\base\InvalidConfigException
     */
    public function httpUpsert(
        TimelineEventMutatorInterface $criteria
    ): ResponseInterface {
        return $this->rawHttpUpsert(
            $criteria->getTypeId(),
            $criteria->getId(),
            $criteria->getPayload(),
            $criteria->getConnection(),
            $criteria->getCache()
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
     * @param TimelineEventMutatorInterface $criteria
     * @return callable
     * @throws \yii\base\InvalidConfigException
     */
    public function httpUpsertRelay(
        TimelineEventMutatorInterface $criteria
    ): callable {
        return $this->rawHttpUpsertRelay(
            $criteria->getTypeId(),
            $criteria->getId(),
            $criteria->getPayload(),
            $criteria->getConnection(),
            $criteria->getCache()
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

        $payload['id'] = $id;
        $payload['eventTypeId'] = $typeId;

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
