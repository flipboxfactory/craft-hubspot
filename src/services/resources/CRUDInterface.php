<?php

/**
 * @copyright  Copyright (c) Flipbox Digital Limited
 * @license    https://flipboxfactory.com/software/hubspot/license
 * @link       https://www.flipboxfactory.com/software/hubspot/
 */

namespace flipbox\hubspot\services\resources;

use craft\base\ElementInterface;
use flipbox\hubspot\builders\ContactBuilder;
use flipbox\hubspot\builders\ObjectBuilderInterface;
use flipbox\hubspot\connections\ConnectionInterface;
use flipbox\hubspot\criteria\ContactCriteria;
use flipbox\hubspot\criteria\ObjectCriteriaInterface;
use flipbox\hubspot\fields\Objects;
use flipbox\hubspot\transformers\collections\TransformerCollectionInterface;
use Flipbox\Relay\HubSpot\Builder\Resources\Contact\Create;
use Flipbox\Relay\HubSpot\Builder\Resources\Contact\Delete;
use Flipbox\Relay\HubSpot\Builder\Resources\Contact\ReadById;
use Flipbox\Relay\HubSpot\Builder\Resources\Contact\Update;
use Psr\Http\Message\ResponseInterface;
use Psr\SimpleCache\CacheInterface;
use yii\base\Component;

/**
 * @author Flipbox Factory <hello@flipboxfactory.com>
 * @since 1.0.0
 */
interface CRUDInterface
{
    /**
     * @param array $config
     * @return ObjectCriteriaInterface
     */
    public function getCriteria(array $config = []): ObjectCriteriaInterface;

    /**
     * @param array $config
     * @return ObjectBuilderInterface
     */
    public function getBuilder(array $config = []): ObjectBuilderInterface;

    /**
     * @param ElementInterface $element
     * @param Objects $field
     * @param ConnectionInterface|string|null $connection
     * @param CacheInterface|string|null $cache
     * @param TransformerCollectionInterface|array|null $transformer
     * @return bool
     */
    public function syncDown(
        ElementInterface $element,
        Objects $field,
        ConnectionInterface $connection = null,
        CacheInterface $cache = null,
        TransformerCollectionInterface $transformer = null
    ): bool;

    /**
     * @param ElementInterface $element
     * @param Objects $field
     * @param ConnectionInterface|string|null $connection
     * @param CacheInterface|string|null $cache
     * @param TransformerCollectionInterface|array|null $transformer
     * @return false|string
     */
    public function syncUp(
        ElementInterface $element,
        Objects $field,
        ConnectionInterface $connection = null,
        CacheInterface $cache = null,
        TransformerCollectionInterface $transformer = null
    ): bool;

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
    );

    /**
     * @param ObjectBuilderInterface $builder
     * @param ConnectionInterface|string|null $connection
     * @return ResponseInterface
     * @throws \yii\base\InvalidConfigException
     */
    public function httpCreate(
        ObjectBuilderInterface $builder,
        ConnectionInterface $connection = null
    ): ResponseInterface;

    /**
     * @param ObjectCriteriaInterface $criteria
     * @param null $source
     * @return mixed
     * @throws \yii\base\InvalidConfigException
     */
    public function read(
        ObjectCriteriaInterface $criteria,
        $source = null
    );

    /**
     * @param ObjectCriteriaInterface $criteria
     * @return ResponseInterface
     * @throws \yii\base\InvalidConfigException
     */
    public function httpRead(
        ObjectCriteriaInterface $criteria
    ): ResponseInterface;

    /**
     * @param ObjectBuilderInterface $builder
     * @param ConnectionInterface|string|null $connection
     * @param CacheInterface|string|null $cache
     * @param TransformerCollectionInterface|array|null $transformer
     * @param null $source
     * @return mixed
     * @throws \yii\base\InvalidConfigException
     */
    public function update(
        ObjectBuilderInterface $builder,
        ConnectionInterface $connection = null,
        CacheInterface $cache = null,
        TransformerCollectionInterface $transformer = null,
        $source = null
    );

    /**
     * @param ObjectBuilderInterface $builder
     * @param ConnectionInterface|string|null $connection
     * @param CacheInterface|string|null $cache
     * @return ResponseInterface
     * @throws \yii\base\InvalidConfigException
     */
    public function httpUpdate(
        ObjectBuilderInterface $builder,
        ConnectionInterface $connection = null,
        CacheInterface $cache = null
    ): ResponseInterface;

    /**
     * @param ObjectBuilderInterface $builder
     * @param ConnectionInterface|string|null $connection
     * @param CacheInterface|string|null $cache
     * @param TransformerCollectionInterface|array|null $transformer
     * @param null $source
     * @return mixed
     * @throws \yii\base\InvalidConfigException
     */
    public function delete(
        ObjectBuilderInterface $builder,
        ConnectionInterface $connection = null,
        CacheInterface $cache = null,
        TransformerCollectionInterface $transformer = null,
        $source = null
    );

    /**
     * @param ObjectBuilderInterface $builder
     * @param ConnectionInterface|string|null $connection
     * @param CacheInterface|string|null $cache
     * @return ResponseInterface
     * @throws \yii\base\InvalidConfigException
     */
    public function httpDelete(
        ObjectBuilderInterface $builder,
        ConnectionInterface $connection = null,
        CacheInterface $cache = null
    ): ResponseInterface;
}
