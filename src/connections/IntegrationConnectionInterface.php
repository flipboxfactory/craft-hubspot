<?php

/**
 * @copyright  Copyright (c) Flipbox Digital Limited
 * @license    https://flipboxfactory.com/software/hubspot/license
 * @link       https://www.flipboxfactory.com/software/hubspot/
 */

namespace flipbox\hubspot\connections;

/**
 * @author Flipbox Factory <hello@flipboxfactory.com>
 * @since 1.0.0
 */
interface IntegrationConnectionInterface extends ConnectionInterface
{
    /**
     * The HubSpot App Id
     *
     * @return string
     */
    public function getAppId(): string;
}
