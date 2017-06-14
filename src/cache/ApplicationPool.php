<?php

namespace flipbox\hubspot\cache;

use flipbox\hubspot\HubSpot;
use Stash\Interfaces\PoolInterface;

class ApplicationPool implements CacheStrategyInterface
{
    /**
     * @return PoolInterface
     */
    public function getPool(): PoolInterface
    {
        return HubSpot::getInstance()->cache()->get('foo');
    }
}