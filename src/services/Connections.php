<?php

/**
 * @copyright  Copyright (c) Flipbox Digital Limited
 * @license    https://flipboxfactory.com/software/hubspot/license
 * @link       https://www.flipboxfactory.com/software/hubspot/
 */

namespace flipbox\craft\hubspot\services;

use flipbox\craft\hubspot\connections\ApplicationKeyConnection;
use flipbox\craft\hubspot\events\RegisterConnectionTypesEvent;
use flipbox\craft\hubspot\HubSpot;
use flipbox\craft\hubspot\records\Connection;
use flipbox\craft\integration\exceptions\ConnectionNotFound;
use flipbox\craft\integration\services\IntegrationConnections;
use Flipbox\HubSpot\Connections\ConnectionInterface;
use Flipbox\HubSpot\Connections\IntegrationConnectionInterface;

/**
 * @author Flipbox Factory <hello@flipboxfactory.com>
 * @since 1.0.0
 */
class Connections extends IntegrationConnections
{
    /**
     * The integration connection handle
     */
    const INTEGRATION_CONNECTION = 'token';

    /**
     * The default connection identifier
     */
    const DEFAULT_INTEGRATION_CONNECTION = 'DEFAULT_INTEGRATION';

    /**
     * The override file
     */
    public $overrideFile = 'hubspot-connections';

    /**
     * @inheritdoc
     */
    protected static function tableName(): string
    {
        return Connection::tableName();
    }

    /**
     * @inheritdoc
     */
    protected static function connectionInstance(): string
    {
        return ConnectionInterface::class;
    }

    /**
     * @inheritdoc
     */
    protected function getDefaultConnection(): string
    {
        return HubSpot::getInstance()->getSettings()->getDefaultConnection();
    }

    /**
     * @param string $handle
     * @param bool $enabledOnly
     * @return IntegrationConnectionInterface|null
     */
    public function findIntegration(
        string $handle = self::DEFAULT_INTEGRATION_CONNECTION,
        bool $enabledOnly = true
    ) {
        if ($handle === self::DEFAULT_INTEGRATION_CONNECTION) {
            $handle = HubSpot::getInstance()->getSettings()->getDefaultIntegrationConnection();
        }

        $connection = $this->find($handle, $enabledOnly);

        if (!$connection instanceof IntegrationConnectionInterface) {
            return null;
        }

        return $connection;
    }

    /**
     * @param string $handle
     * @param bool $enabledOnly
     * @return IntegrationConnectionInterface
     * @throws ConnectionNotFound
     */
    public function getIntegration(
        string $handle = self::DEFAULT_INTEGRATION_CONNECTION,
        bool $enabledOnly = true
    ): IntegrationConnectionInterface {
        if (null === ($connection = $this->findIntegration($handle, $enabledOnly))) {
            throw new ConnectionNotFound('Unable to find connection');
        }

        return $connection;
    }

    /**
     * @var ConnectionInterface[]
     */
    private $types;

    /**
     * @return ConnectionInterface[]
     */
    public function getTypes(): array
    {
        if ($this->types === null) {
            $event = new RegisterConnectionTypesEvent([
                'types' => [
                    ApplicationKeyConnection::class
                ]
            ]);

            $this->trigger(
                $event::REGISTER_CONNECTIONS,
                $event
            );

            $this->types = $event->types;
        }

        return $this->types;
    }
}
