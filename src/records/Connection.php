<?php

/**
 * @copyright  Copyright (c) Flipbox Digital Limited
 * @license    https://flipboxfactory.com/software/hubspot/license
 * @link       https://www.flipboxfactory.com/software/hubspot/
 */

namespace flipbox\craft\hubspot\records;

use craft\helpers\Component as ComponentHelper;
use flipbox\craft\hubspot\HubSpot;
use flipbox\craft\hubspot\validators\ConnectionValidator;
use flipbox\craft\integration\records\IntegrationConnection;
use Flipbox\HubSpot\Connections\ConnectionInterface;
use Flipbox\Skeleton\Helpers\ObjectHelper;

/**
 * @author Flipbox Factory <hello@flipboxfactory.com>
 * @since 1.0.0
 */
class Connection extends IntegrationConnection
{
    /**
     * The table name
     */
    const TABLE_ALIAS = 'hubspot_connections';

    /**
     * @var ConnectionInterface|null
     */
    private $connection;

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return array_merge(
            parent::rules(),
            [
                [
                    [
                        'class'
                    ],
                    ConnectionValidator::class
                ]
            ]
        );
    }

    /**
     * @noinspection PhpDocMissingThrowsInspection
     * @return ConnectionInterface
     */
    public function getConnection(): ConnectionInterface
    {
        if (null === $this->connection) {
            $config = ComponentHelper::mergeSettings([
                'settings' => $this->settings,
                'class' => $this->class
            ]);

            // Apply overrides
            if (!empty($this->handle) && null !== ($override = HubSpot::getInstance()->getConnections()->getOverrides($this->handle))) {
                $config = array_merge($config, $override);
            }

            /** @noinspection PhpUnhandledExceptionInspection */
            $this->connection = ObjectHelper::create(
                $config,
                ConnectionInterface::class
            );
        }

        return $this->connection;
    }
}
