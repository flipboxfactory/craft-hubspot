<?php

namespace flipbox\hubspot\models;

use flipbox\hubspot\authentication\ApplicationKey;
use flipbox\hubspot\authentication\AuthenticationStrategyInterface;
use flipbox\hubspot\cache\ApplicationPool;
use flipbox\hubspot\cache\CacheStrategyInterface;
use yii\base\Model;

class Settings extends Model
{
    /**
     * @var string
     */
    public $apiKey;

    public $userIdFieldHandle;

    /**
     * @return AuthenticationStrategyInterface
     */
    public function getAuthenticationStrategy(): AuthenticationStrategyInterface
    {
        return new ApplicationKey();
    }

    /**
     * @return CacheStrategyInterface
     */
    public function getCacheStrategy(): CacheStrategyInterface
    {
        return new ApplicationPool();
    }
}
