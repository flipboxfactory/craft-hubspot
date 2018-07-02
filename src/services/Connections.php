<?php

/**
 * @copyright  Copyright (c) Flipbox Digital Limited
 * @license    https://flipboxfactory.com/software/hubspot/license
 * @link       https://www.flipboxfactory.com/software/hubspot/
 */

namespace flipbox\hubspot\services;

use flipbox\hubspot\connections\ConnectionInterface;
use flipbox\hubspot\events\RegisterConnectionsEvent;
use flipbox\hubspot\HubSpot;
use yii\base\InvalidConfigException;
use yii\di\ServiceLocator;

/**
 * @author Flipbox Factory <hello@flipboxfactory.com>
 * @since 1.0.0
 */
class Connections extends ServiceLocator
{
    /**
     * @event RegisterConnectionsEvent The event that is triggered when registering connections.
     */
    const EVENT_REGISTER_CONNECTIONS = 'registerConnections';

    /**
     * The default connection handle
     */
    const APP_CONNECTION = 'app';

    /**
     * The default connection handle
     */
    const INTEGRATION_CONNECTION = 'token';

    /**
     * The default connection identifier
     */
    const DEFAULT_CONNECTION = 'DEFAULT_APP';

    /**
     * The default connection identifier
     */
    const DEFAULT_INTEGRATION_CONNECTION = 'DEFAULT_INTEGRATION';

    /**
     * @inheritdoc
     */
    public function init()
    {
        parent::init();

        $event = new RegisterConnectionsEvent([
            'connections' => []
        ]);

        $this->trigger(self::EVENT_REGISTER_CONNECTIONS, $event);

        $this->setComponents(
            $event->connections
        );
    }

    /**
     * @inheritdoc
     * @return ConnectionInterface
     */
    public function get($id, $throwException = true)
    {
        switch ($id) {
            case self::DEFAULT_CONNECTION:
                $id = HubSpot::getInstance()->getSettings()->getDefaultConnection();
                break;

            case self::DEFAULT_INTEGRATION_CONNECTION:
                $id = HubSpot::getInstance()->getSettings()->getDefaultIntegrationConnection();
                break;
        }

        $connection = parent::get($id, $throwException);

        if (!$connection instanceof ConnectionInterface) {
            throw new InvalidConfigException(sprintf(
                "Connection '%s' must be an instance of '%s', '%s' given.",
                (string)$id,
                ConnectionInterface::class,
                get_class($connection)
            ));
        }
        return $connection;
    }

    /**
     * @param bool $throwException
     * @return ConnectionInterface[]
     * @throws InvalidConfigException
     */
    public function getAll($throwException = true)
    {
        $components = [];

        foreach ($this->getComponents(true) as $id => $component) {
            $components[$id] = $this->get($id, $throwException);
        }

        return $components;
    }
}
