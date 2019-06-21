<?php

/**
 * @copyright  Copyright (c) Flipbox Digital Limited
 * @license    https://flipboxfactory.com/software/hubspot/license
 * @link       https://www.flipboxfactory.com/software/hubspot/
 */

namespace flipbox\craft\hubspot\criteria;

use flipbox\craft\hubspot\HubSpot;
use Flipbox\HubSpot\Connections\ConnectionInterface;

/**
 * @author Flipbox Factory <hello@flipboxfactory.com>
 * @since 1.0.0
 */
trait IntegrationConnectionTrait
{
    use \Flipbox\HubSpot\Criteria\IntegrationConnectionTrait;

    /**
     * @param $connection
     * @return ConnectionInterface
     * @throws \flipbox\craft\integration\exceptions\ConnectionNotFound
     */
    protected static function resolveConnection($connection): ConnectionInterface
    {
        if ($connection instanceof ConnectionInterface) {
            return $connection;
        }

        if ($connection === null) {
            $connection = HubSpot::getInstance()->getSettings()->getDefaultIntegrationConnection();
        }

        return HubSpot::getInstance()->getConnections()->getIntegration($connection);
    }
}
