<?php

namespace flipbox\hubspot\cache;

use flipbox\hubspot\HubSpot;
use Stash\Interfaces\PoolInterface;
use yii\base\Object;

class CacheStrategy extends Object implements CacheStrategyInterface
{
    /**
     * @var int
     */
    public $duration;

    /**
     * @var string
     */
    public $type;

    /**
     * @return PoolInterface
     */
    public function getPool(): PoolInterface
    {
        return HubSpot::getInstance()->cache()->get($this->type);
    }
}
