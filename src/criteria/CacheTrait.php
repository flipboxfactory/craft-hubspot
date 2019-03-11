<?php

/**
 * @copyright  Copyright (c) Flipbox Digital Limited
 * @license    https://flipboxfactory.com/software/hubspot/license
 * @link       https://www.flipboxfactory.com/software/hubspot/
 */

namespace flipbox\craft\hubspot\criteria;

use flipbox\craft\hubspot\HubSpot;
use Psr\SimpleCache\CacheInterface;

/**
 * @author Flipbox Factory <hello@flipboxfactory.com>
 * @since 1.0.0
 */
trait CacheTrait
{
    use \Flipbox\HubSpot\Criteria\CacheTrait;

    /**
     * @inheritdoc
     */
    protected function resolveCache($cache): CacheInterface
    {
        if ($cache instanceof CacheInterface) {
            return $cache;
        }

        if ($cache === null) {
            $cache = HubSpot::getInstance()->getSettings()->getDefaultCache();
        }

        return HubSpot::getInstance()->getCache()->get($cache);
    }
}
