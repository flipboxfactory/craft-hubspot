<?php

/**
 * @copyright  Copyright (c) Flipbox Digital Limited
 * @license    https://flipboxfactory.com/software/hubspot/license
 * @link       https://www.flipboxfactory.com/software/hubspot/
 */

namespace flipbox\hubspot\helpers;

use flipbox\hubspot\connections\ConnectionInterface;
use flipbox\hubspot\connections\IntegrationConnectionInterface;
use flipbox\hubspot\HubSpot;
use flipbox\hubspot\services\Connections;
use yii\base\InvalidConfigException;

/**
 * @author Flipbox Factory <hello@flipboxfactory.com>
 * @since 1.0.0
 */
class ConnectionHelper
{
    /**
     * @param null|string|ConnectionInterface $connection
     * @return ConnectionInterface
     * @throws InvalidConfigException
     */
    public static function resolveConnection($connection): ConnectionInterface
    {
        if ($connection instanceof ConnectionInterface) {
            return $connection;
        }

        if ($connection === null) {
            $connection = Connections::DEFAULT_CONNECTION;
        }

        return HubSpot::getInstance()->getConnections()->get($connection);
    }

    /**
     * @param null|string|IntegrationConnectionInterface $connection
     * @return IntegrationConnectionInterface
     * @throws InvalidConfigException
     */
    public static function resolveIntegrationConnection($connection): IntegrationConnectionInterface
    {
        if ($connection instanceof IntegrationConnectionInterface) {
            return $connection;
        }

        if ($connection === null) {
            $connection = Connections::DEFAULT_INTEGRATION_CONNECTION;
        }

        $connection = HubSpot::getInstance()->getConnections()->get($connection);

        if (!$connection instanceof IntegrationConnectionInterface) {
            throw new InvalidConfigException(sprintf(
                "Integration Connection must be an instance of '%s', '%s' given.",
                IntegrationConnectionInterface::class,
                get_class($connection)
            ));
        }

        return $connection;
    }
}
