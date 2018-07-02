<?php

/**
 * @copyright  Copyright (c) Flipbox Digital Limited
 * @license    https://flipboxfactory.com/software/hubspot/license
 * @link       https://www.flipboxfactory.com/software/hubspot/
 */

namespace flipbox\hubspot\helpers;

use flipbox\hubspot\HubSpot;
use flipbox\hubspot\services\Cache;
use Psr\SimpleCache\CacheInterface;

/**
 * @author Flipbox Factory <hello@flipboxfactory.com>
 * @since 1.0.0
 */
class CacheHelper
{
    /**
     * @param null|string|CacheInterface $cache
     * @return CacheInterface
     * @throws \yii\base\InvalidConfigException
     */
    public static function resolveCache($cache): CacheInterface
    {
        if ($cache instanceof CacheInterface) {
            return $cache;
        }

        if ($cache === null) {
            $cache = Cache::DEFAULT_CACHE;
        }

        return HubSpot::getInstance()->getCache()->get($cache);
    }
}
