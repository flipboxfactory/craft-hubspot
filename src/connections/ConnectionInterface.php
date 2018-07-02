<?php

/**
 * @copyright  Copyright (c) Flipbox Digital Limited
 * @license    https://flipboxfactory.com/software/hubspot/license
 * @link       https://www.flipboxfactory.com/software/hubspot/
 */

namespace flipbox\hubspot\connections;

use Flipbox\Relay\HubSpot\AuthorizationInterface;

/**
 * @author Flipbox Factory <hello@flipboxfactory.com>
 * @since 1.0.0
 */
interface ConnectionInterface extends AuthorizationInterface
{
    /**
     * The HubSpot Id
     *
     * @return string
     */
    public function getHubId(): string;
}
