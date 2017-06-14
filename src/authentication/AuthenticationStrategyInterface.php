<?php

namespace flipbox\hubspot\authentication;

use Relay\MiddlewareInterface;

interface AuthenticationStrategyInterface
{

    /**
     * @return array|MiddlewareInterface
     */
    public function getMiddleware();

}
