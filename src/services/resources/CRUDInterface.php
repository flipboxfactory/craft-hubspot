<?php

/**
 * @copyright  Copyright (c) Flipbox Digital Limited
 * @license    https://flipboxfactory.com/software/hubspot/license
 * @link       https://www.flipboxfactory.com/software/hubspot/
 */

namespace flipbox\hubspot\services\resources;

use craft\base\ElementInterface;
use flipbox\hubspot\criteria\ObjectMutatorInterface;
use flipbox\hubspot\connections\ConnectionInterface;
use flipbox\hubspot\criteria\ObjectAccessorInterface;
use flipbox\hubspot\fields\Objects;
use flipbox\hubspot\transformers\collections\TransformerCollectionInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\SimpleCache\CacheInterface;

/**
 * @author Flipbox Factory <hello@flipboxfactory.com>
 * @since 1.0.0
 */
interface CRUDInterface
{
    /**
     * @return array|TransformerCollectionInterface
     */
    public static function defaultTransformer();

    /**
     * @param array $config
     * @return ObjectAccessorInterface
     */
    public function getAccessorCriteria(array $config = []): ObjectAccessorInterface;

    /**
     * @param array $config
     * @return ObjectMutatorInterface
     */
    public function getMutatorCriteria(array $config = []): ObjectMutatorInterface;

    /**
     * @param ElementInterface $element
     * @param Objects $field
     * @param ConnectionInterface|string|null $connection
     * @param CacheInterface|string|null $cache
     * @return bool
     */
    public function syncDown(
        ElementInterface $element,
        Objects $field,
        ConnectionInterface $connection = null,
        CacheInterface $cache = null
    ): bool;

    /**
     * @param ElementInterface $element
     * @param Objects $field
     * @param ConnectionInterface|string|null $connection
     * @param CacheInterface|string|null $cache
     * @return false|string
     */
    public function syncUp(
        ElementInterface $element,
        Objects $field,
        ConnectionInterface $connection = null,
        CacheInterface $cache = null
    ): bool;

    /**
     * @param ObjectMutatorInterface $criteria
     * @param null $source
     * @return mixed
     * @throws \yii\base\InvalidConfigException
     */
    public function create(
        ObjectMutatorInterface $criteria,
        $source = null
    );

    /**
     * @param ObjectMutatorInterface $criteria
     * @return ResponseInterface
     * @throws \yii\base\InvalidConfigException
     */
    public function httpCreate(
        ObjectMutatorInterface $criteria
    ): ResponseInterface;

    /**
     * @param ObjectAccessorInterface $criteria
     * @param null $source
     * @return mixed
     * @throws \yii\base\InvalidConfigException
     */
    public function read(
        ObjectAccessorInterface $criteria,
        $source = null
    );

    /**
     * @param ObjectAccessorInterface $criteria
     * @return ResponseInterface
     * @throws \yii\base\InvalidConfigException
     */
    public function httpRead(
        ObjectAccessorInterface $criteria
    ): ResponseInterface;

    /**
     * @param ObjectMutatorInterface $criteria
     * @param null $source
     * @return mixed
     * @throws \yii\base\InvalidConfigException
     */
    public function update(
        ObjectMutatorInterface $criteria,
        $source = null
    );

    /**
     * @param ObjectMutatorInterface $criteria
     * @return ResponseInterface
     * @throws \yii\base\InvalidConfigException
     */
    public function httpUpdate(
        ObjectMutatorInterface $criteria
    ): ResponseInterface;

    /**
     * @param ObjectMutatorInterface $criteria
     * @param null $source
     * @return mixed
     * @throws \yii\base\InvalidConfigException
     */
    public function delete(
        ObjectMutatorInterface $criteria,
        $source = null
    );

    /**
     * @param ObjectMutatorInterface $criteria
     * @return ResponseInterface
     * @throws \yii\base\InvalidConfigException
     */
    public function httpDelete(
        ObjectMutatorInterface $criteria
    ): ResponseInterface;
}
