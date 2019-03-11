<?php

/**
 * @copyright  Copyright (c) Flipbox Digital Limited
 * @license    https://flipboxfactory.com/software/hubspot/license
 * @link       https://www.flipboxfactory.com/software/hubspot/
 */

namespace flipbox\craft\hubspot\cp;

use Craft;
use flipbox\craft\hubspot\connections\ApplicationKeyConnection;
use flipbox\craft\hubspot\connections\SavableConnectionInterface;
use flipbox\craft\hubspot\events\RegisterConnectionsEvent;
use flipbox\hubspot\HubSpot;
use yii\base\Module as BaseModule;
use yii\web\NotFoundHttpException;

/**
 * @author Flipbox Factory <hello@flipboxfactory.com>
 * @since 1.0.0
 *
 * @property HubSpot $module
 */
class Cp extends BaseModule
{
    /**
     * @var SavableConnectionInterface[]
     */
    private $registeredConnections;

    /**
     * @inheritdoc
     * @throws NotFoundHttpException
     */
    public function beforeAction($action)
    {
        if (!Craft::$app->request->getIsCpRequest()) {
            throw new NotFoundHttpException();
        }

        return parent::beforeAction($action);
    }

    /*******************************************
     * CONNECTIONS
     *******************************************/

    /**
     * @return SavableConnectionInterface[]
     */
    public function getAvailableConnections(): array
    {
        if ($this->registeredConnections === null) {
            $event = new RegisterConnectionsEvent([
                'connections' => [
                    ApplicationKeyConnection::class
                ]
            ]);

            $this->trigger(
                $event::REGISTER_CONNECTIONS,
                $event
            );

            $this->registeredConnections = $event->connections;
        }

        return $this->registeredConnections;
    }
}
