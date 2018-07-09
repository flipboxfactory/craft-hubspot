<?php

/**
 * @copyright  Copyright (c) Flipbox Digital Limited
 * @license    https://flipboxfactory.com/software/hubspot/license
 * @link       https://www.flipboxfactory.com/software/hubspot/
 */

namespace flipbox\hubspot\services\resources\traits;

use flipbox\hubspot\connections\ConnectionInterface;
use flipbox\hubspot\criteria\ObjectMutatorInterface;
use flipbox\hubspot\transformers\collections\TransformerCollectionInterface;
use League\Pipeline\PipelineBuilderInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\SimpleCache\CacheInterface;

/**
 * @author Flipbox Factory <hello@flipboxfactory.com>
 * @since 1.0.0
 */
trait UpsertObjectTrait
{
    use UpdateObjectTrait,
        CreateObjectTrait;

    /**
     * @param string|null $identifier
     * @return bool
     */
    protected function upsertHasId(string $identifier = null): bool
    {
        return !empty($identifier);
    }

    /**
     * @param ObjectMutatorInterface $criteria
     * @param null $source
     * @return mixed
     * @throws \yii\base\InvalidConfigException
     */
    public function upsert(
        ObjectMutatorInterface $criteria,
        $source = null
    ) {
        return $this->rawUpsert(
            $criteria->getPayload(),
            $criteria->getId(),
            $criteria->getConnection(),
            $criteria->getCache(),
            $criteria->getTransformer(),
            $source
        );
    }

    /**
     * @param array $payload
     * @param string|null $identifier
     * @param ConnectionInterface|string|null $connection
     * @param CacheInterface|string|null $cache
     * @param TransformerCollectionInterface|array|null $transformer
     * @param null $source
     * @return mixed
     * @throws \yii\base\InvalidConfigException
     */
    public function rawUpsert(
        array $payload,
        string $identifier = null,
        ConnectionInterface $connection = null,
        CacheInterface $cache = null,
        TransformerCollectionInterface $transformer = null,
        $source = null
    ) {
        if (!$this->upsertHasId($identifier)) {
            return $this->rawCreate(
                $payload,
                $connection,
                $transformer,
                $source
            );
        }

        return $this->rawUpdate(
            $identifier,
            $payload,
            $connection,
            $cache,
            $transformer,
            $source
        );
    }

    /**
     * @param ObjectMutatorInterface $criteria
     * @return PipelineBuilderInterface
     * @throws \yii\base\InvalidConfigException
     */
    public function upsertPipeline(
        ObjectMutatorInterface $criteria
    ): PipelineBuilderInterface {
        return $this->rawUpsertPipeline(
            $criteria->getPayload(),
            $criteria->getId(),
            $criteria->getConnection(),
            $criteria->getCache(),
            $criteria->getTransformer()
        );
    }

    /**
     * @param array $payload
     * @param string|null $identifier
     * @param ConnectionInterface|string|null $connection
     * @param CacheInterface|string|null $cache
     * @param TransformerCollectionInterface|array|null $transformer
     * @return PipelineBuilderInterface
     * @throws \yii\base\InvalidConfigException
     */
    public function rawUpsertPipeline(
        array $payload,
        string $identifier = null,
        ConnectionInterface $connection = null,
        CacheInterface $cache = null,
        TransformerCollectionInterface $transformer = null
    ): PipelineBuilderInterface {
        if (!$this->upsertHasId($identifier)) {
            return $this->rawCreatePipeline(
                $payload,
                $connection,
                $transformer
            );
        }

        return $this->rawUpdatePipeline(
            $identifier,
            $payload,
            $connection,
            $cache,
            $transformer
        );
    }

    /**
     * @param ObjectMutatorInterface $criteria
     * @return callable
     * @throws \yii\base\InvalidConfigException
     */
    public function httpUpsertRelay(
        ObjectMutatorInterface $criteria
    ): callable {
        return $this->rawHttpUpsertRelay(
            $criteria->getPayload(),
            $criteria->getId(),
            $criteria->getConnection(),
            $criteria->getCache()
        );
    }

    /**
     * @param array $payload
     * @param string|null $identifier
     * @param ConnectionInterface|string|null $connection
     * @param CacheInterface|string|null $cache
     * @return callable
     * @throws \yii\base\InvalidConfigException
     */
    public function rawHttpUpsertRelay(
        array $payload,
        string $identifier = null,
        ConnectionInterface $connection = null,
        CacheInterface $cache = null
    ): callable {
        if (!$this->upsertHasId($identifier)) {
            return $this->rawHttpCreateRelay(
                $payload,
                $connection
            );
        }

        return $this->rawHttpUpdateRelay(
            $identifier,
            $payload,
            $connection,
            $cache
        );
    }

    /**
     * @param ObjectMutatorInterface $criteria
     * @return ResponseInterface
     * @throws \yii\base\InvalidConfigException
     */
    public function httpUpsert(
        ObjectMutatorInterface $criteria
    ): ResponseInterface {
        return $this->rawHttpUpsert(
            $criteria->getPayload(),
            $criteria->getId(),
            $criteria->getConnection(),
            $criteria->getCache()
        );
    }

    /**
     * @param array $payload
     * @param string|null $identifier
     * @param ConnectionInterface|string|null $connection
     * @param CacheInterface|string|null $cache
     * @return ResponseInterface
     * @throws \yii\base\InvalidConfigException
     */
    public function rawHttpUpsert(
        array $payload,
        string $identifier = null,
        ConnectionInterface $connection = null,
        CacheInterface $cache = null
    ): ResponseInterface {
        if (!$this->upsertHasId($identifier)) {
            return $this->rawHttpCreate(
                $payload,
                $connection
            );
        }

        return $this->rawHttpUpdate(
            $identifier,
            $payload,
            $connection,
            $cache
        );
    }
}
