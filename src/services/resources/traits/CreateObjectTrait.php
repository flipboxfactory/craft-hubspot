<?php

/**
 * @copyright  Copyright (c) Flipbox Digital Limited
 * @license    https://flipboxfactory.com/software/hubspot/license
 * @link       https://www.flipboxfactory.com/software/hubspot/
 */

namespace flipbox\hubspot\services\resources\traits;

use flipbox\hubspot\builders\ObjectBuilderInterface;
use flipbox\hubspot\connections\ConnectionInterface;
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
     * @param ObjectBuilderInterface $builder
     * @param ConnectionInterface|string|null $connection
     * @param TransformerCollectionInterface|array|null $transformer
     * @param null $source
     * @return mixed
     * @throws \yii\base\InvalidConfigException
     */
    public function create(
        ObjectBuilderInterface $builder,
        ConnectionInterface $connection = null,
        TransformerCollectionInterface $transformer = null,
        $source = null
    ) {
        return $this->rawCreate(
            $builder->getPayload(),
            $connection,
            $transformer,
            $source
        );
    }

    /**
     * @param array $payload
     * @param ConnectionInterface|string|null $connection
     * @param TransformerCollectionInterface|array|null $transformer
     * @param null $source
     * @return mixed
     * @throws \yii\base\InvalidConfigException
     */
    public function rawCreate(
        array $payload,
        ConnectionInterface $connection = null,
        TransformerCollectionInterface $transformer = null,
        $source = null
    ) {
        return $this->rawCreatePipeline(
            $payload,
            $connection,
            $transformer
        )($source);
    }

    /**
     * @param ObjectBuilderInterface $builder
     * @param ConnectionInterface|string|null $connection
     * @param TransformerCollectionInterface|array|null $transformer
     * @return PipelineBuilderInterface
     * @throws \yii\base\InvalidConfigException
     */
    public function createPipeline(
        ObjectBuilderInterface $builder,
        ConnectionInterface $connection = null,
        TransformerCollectionInterface $transformer = null
    ): PipelineBuilderInterface {
        return $this->rawCreatePipeline(
            $builder->getPayload(),
            $connection,
            $transformer
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
     * @param ObjectBuilderInterface $builder
     * @param ConnectionInterface|string|null $connection
     * @return callable
     * @throws \yii\base\InvalidConfigException
     */
    public function httpCreateRelay(
        ObjectBuilderInterface $builder,
        ConnectionInterface $connection = null
    ): callable {
        return $this->rawHttpCreateRelay(
            $builder->getPayload(),
            $connection
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

        /** @var RelayBuilderInterface $builder */
        $builder = new $class(
            $payload,
            ConnectionHelper::resolveConnection($connection),
            HubSpot::getInstance()->getPsrLogger()
        );

        return $builder->build();
    }

    /**
     * @param ObjectBuilderInterface $builder
     * @param ConnectionInterface|string|null $connection
     * @return ResponseInterface
     * @throws \yii\base\InvalidConfigException
     */
    public function httpCreate(
        ObjectBuilderInterface $builder,
        ConnectionInterface $connection = null
    ): ResponseInterface {
        return $this->rawHttpCreateRelay(
            $builder->getPayload(),
            $connection
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
