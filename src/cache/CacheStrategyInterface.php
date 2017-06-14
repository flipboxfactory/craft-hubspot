<?php

namespace flipbox\hubspot\cache;

use Stash\Interfaces\PoolInterface;

interface CacheStrategyInterface
{
    /**
     * @return PoolInterface
     */
    public function getPool(): PoolInterface;
}
