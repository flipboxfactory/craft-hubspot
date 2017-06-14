<?php

namespace flipbox\hubspot\authentication;

use flipbox\hubspot\HubSpot;
use Flipbox\Relay\HubSpot\Middleware\Authorization\Key;

class ApplicationKey implements AuthenticationStrategyInterface
{
    /**
     * @return array
     */
    public function getMiddleware()
    {
        return [
            'class' => Key::class,
            'key' => HubSpot::getInstance()->getSettings()->apiKey,
            'logger' => HubSpot::getInstance()->logger()
        ];
    }
}
