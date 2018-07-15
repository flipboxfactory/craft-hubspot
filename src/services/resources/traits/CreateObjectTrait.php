<?php

/**
 * @copyright  Copyright (c) Flipbox Digital Limited
 * @license    https://flipboxfactory.com/software/hubspot/license
 * @link       https://www.flipboxfactory.com/software/hubspot/
 */

namespace flipbox\hubspot\services\resources\traits;

use flipbox\hubspot\connections\ConnectionInterface;
use flipbox\hubspot\criteria\ObjectMutatorInterface;
use flipbox\hubspot\helpers\ConnectionHelper;
use flipbox\hubspot\helpers\TransformerHelper;
use flipbox\hubspot\HubSpot;
use flipbox\hubspot\pipeline\Resource;
use flipbox\hubspot\transformers\collections\TransformerCollectionInterface;
use Flipbox\Relay\Builder\RelayBuilderInterface;
use League\Pipeline\PipelineBuilderInterface;
use Psr\Http\Message\ResponseInterface;

/**
 * @author Flipbox Factory <hello@flipboxfactory.com>
 * @since 1.0.0
 */
trait CreateObjectTrait
{
    /**
     * @return string
     */
    protected abstract static function createRelayBuilderClass(): string;

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
    public function create(
        ObjectMutatorInterface $criteria,
        array $extra = []
    ) {
        return $this->rawCreate(
            $criteria->getPayload(),
            $criteria->getConnection(),
            $criteria->getTransformer(),
            $extra
        );
    }

    /**
     * @param array $payload
     * @param ConnectionInterface|string|null $connection
     * @param TransformerCollectionInterface|array|null $transformer
     * @param array $extra
     * @return mixed
     * @throws \yii\base\InvalidConfigException
     */
    public function rawCreate(
        array $payload,
        ConnectionInterface $connection = null,
        TransformerCollectionInterface $transformer = null,
        array $extra = []
    ) {
        return $this->rawCreatePipeline(
            $payload,
            $connection,
            $transformer
        )($extra);
    }

    /**
     * @param ObjectMutatorInterface $criteria
     * @return PipelineBuilderInterface
     * @throws \yii\base\InvalidConfigException
     */
    public function createPipeline(
        ObjectMutatorInterface $criteria
    ): PipelineBuilderInterface {
        return $this->rawCreatePipeline(
            $criteria->getPayload(),
            $criteria->getConnection(),
            $criteria->getTransformer()
        );
    }

    /**
     * @param array $payload
     * @param ConnectionInterface|string|null $connection
     * @param TransformerCollectionInterface|array|null $transformer
     * @return PipelineBuilderInterface
     * @throws \yii\base\InvalidConfigException
     */
    public function rawCreatePipeline(
        array $payload,
        ConnectionInterface $connection = null,
        TransformerCollectionInterface $transformer = null
    ): PipelineBuilderInterface {
        $transformer = TransformerHelper::populateTransformerCollection(
            TransformerHelper::resolveCollection($transformer, static::defaultTransformer()),
            [
                'resource' => [static::createRelayBuilderClass()]
            ]
        );

        return (new Resource(
            $this->rawHttpCreateRelay(
                $payload,
                ConnectionHelper::resolveConnection($connection)
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
    public function httpCreateRelay(
        ObjectMutatorInterface $criteria
    ): callable {
        return $this->rawHttpCreateRelay(
            $criteria->getPayload(),
            $criteria->getConnection()
        );
    }

    /**
     * @param array $payload
     * @param ConnectionInterface|string|null $connection
     * @return callable
     * @throws \yii\base\InvalidConfigException
     */
    public function rawHttpCreateRelay(
        array $payload,
        ConnectionInterface $connection = null
    ): callable {
        $class = static::createRelayBuilderClass();

        /** @var RelayBuilderInterface $criteria */
        $criteria = new $class(
            $payload,
            ConnectionHelper::resolveConnection($connection),
            HubSpot::getInstance()->getPsrLogger()
        );

        return $criteria->build();
    }

    /**
     * @param ObjectMutatorInterface $criteria
     * @return ResponseInterface
     * @throws \yii\base\InvalidConfigException
     */
    public function httpCreate(
        ObjectMutatorInterface $criteria
    ): ResponseInterface {
        return $this->rawHttpCreateRelay(
            $criteria->getPayload(),
            $criteria->getConnection()
        )();
    }

    /**
     * @param array $payload
     * @param ConnectionInterface|string|null $connection
     * @return ResponseInterface
     * @throws \yii\base\InvalidConfigException
     */
    public function rawHttpCreate(
        array $payload,
        ConnectionInterface $connection = null
    ): ResponseInterface {
        return $this->rawHttpCreateRelay(
            $payload,
            $connection
        )();
    }
}
