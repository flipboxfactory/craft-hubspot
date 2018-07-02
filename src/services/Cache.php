<?php

/**
 * @copyright  Copyright (c) Flipbox Digital Limited
 * @license    https://flipboxfactory.com/software/hubspot/license
 * @link       https://www.flipboxfactory.com/software/hubspot/
 */

namespace flipbox\hubspot\services;

use Craft;
use flipbox\craft\psr16\SimpleCacheAdapter;
use flipbox\hubspot\events\RegisterCacheEvent;
use flipbox\hubspot\HubSpot;
use yii\base\InvalidConfigException;
use yii\caching\CacheInterface;
use yii\caching\DummyCache;
use yii\di\ServiceLocator;

/**
 * @author Flipbox Factory <hello@flipboxfactory.com>
 * @since 1.0.0
 */
class Cache extends ServiceLocator
{
    /**
     * @event RegisterConnectionsEvent The event that is triggered when registering caches.
     */
    const EVENT_REGISTER_CACHE = 'registerCache';

    /**
     * The dummy cache handle
     */
    const DUMMY_CACHE = 'dummy';

    /**
     * The app cache handle
     */
    const APP_CACHE = 'app';

    /**
     * The default cache identifier
     */
    const DEFAULT_CACHE = 'DEFAULT';

    /**
     * @inheritdoc
     */
    public function init()
    {
        parent::init();

        $event = new RegisterCacheEvent([
            'cache' => [
                self::APP_CACHE => Craft::$app->getCache(),
                self::DUMMY_CACHE => [
                    'class' => DummyCache::class
                ]
            ]
        ]);

        $this->trigger(self::EVENT_REGISTER_CACHE, $event);

        $this->setComponents(
            $event->cache
        );
    }

    /**
     * @inheritdoc
     * @return SimpleCacheAdapter
     */
    public function get($id, $throwException = true)
    {
        if ($id === self::DEFAULT_CACHE) {
            $id = HubSpot::getInstance()->getSettings()->getDefaultCache();
        }

        return $this->resolveSimpleCache(
            parent::get($id, $throwException),
            $id
        );
    }

    /**
     * @param $cache
     * @param string $id
     * @return SimpleCacheAdapter
     * @throws InvalidConfigException
     */
    private function resolveSimpleCache($cache, string $id): SimpleCacheAdapter
    {
        if ($cache instanceof SimpleCacheAdapter) {
            return $cache;
        }

        if (!$cache instanceof CacheInterface) {
            throw new InvalidConfigException(sprintf(
                "Cache '%s' must be an instance of '%s', '%s' given.",
                (string)$id,
                CacheInterface::class,
                get_class($cache)
            ));
        }

        /** @var SimpleCacheAdapter $cacheAdapter */
        $cacheAdapter = Craft::createObject([
            'class' => SimpleCacheAdapter::class,
            'cache' => $cache
        ]);

        $this->set($id, $cacheAdapter);

        return $cacheAdapter;
    }

    /**
     * @param bool $throwException
     * @return CacheInterface[]
     * @throws InvalidConfigException
     */
    public function getAll($throwException = true)
    {
        $components = [];

        foreach ($this->getComponents(true) as $id => $component) {
            $components[$id] = $this->get($id, $throwException);
        }

        return $components;
    }
}
