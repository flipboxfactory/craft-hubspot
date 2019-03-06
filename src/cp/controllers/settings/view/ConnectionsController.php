<?php

/**
 * @copyright  Copyright (c) Flipbox Digital Limited
 * @license    https://flipboxfactory.com/software/hubspot/license
 * @link       https://www.flipboxfactory.com/software/hubspot/
 */

namespace flipbox\craft\hubspot\cp\controllers\settings\view;

use Craft;
use craft\helpers\UrlHelper;
use flipbox\craft\hubspot\connections\SavableConnectionInterface;
use flipbox\craft\hubspot\HubSpot;
use flipbox\craft\hubspot\records\Connection;
use yii\di\Instance;
use yii\web\Response;

/**
 * @author Flipbox Factory <hello@flipboxfactory.com>
 * @since 1.0.0
 */
class ConnectionsController extends AbstractController
{
    /**
     * The template base path
     */
    const TEMPLATE_BASE = parent::TEMPLATE_BASE . '/connections';

    /**
     * The index view template path
     */
    const TEMPLATE_INDEX = self::TEMPLATE_BASE . '/index';

    /**
     * The index view template path
     */
    const TEMPLATE_UPSERT = self::TEMPLATE_BASE . '/upsert';

    /**
     * @return Response
     * @throws \yii\base\InvalidConfigException
     */
    public function actionIndex(): Response
    {
        $variables = [];
        $this->baseVariables($variables);

        $variables['connections'] = Connection::find()->all();
        $variables['types'] = $this->getAvailableConnections();

        return $this->renderTemplate(
            static::TEMPLATE_INDEX,
            $variables
        );
    }

    /**
     * @param null $identifier
     * @param Connection|null $connection
     * @return Response
     * @throws \craft\errors\MissingComponentException
     * @throws \yii\base\InvalidConfigException
     */
    public function actionUpsert($identifier = null, Connection $connection = null): Response
    {
        if (null === $connection) {
            if (null !== $identifier) {
                $connection = Connection::findOne($identifier);
            } else {
                $connection = new Connection;
            }
        }

        // Bad connection
        if (null === $connection) {
            $message = HubSpot::t(
                "Connection '{$identifier}' is not configurable",
                [
                    'identifier' => $identifier
                ]
            );
            Craft::$app->getSession()->setError($message);
            $this->redirect($this->getBaseCpPath());
        }

        $variables = [];
        if ($connection === null || $connection->getIsNewRecord()) {
            $this->insertVariables($variables);
        } else {
            $this->updateVariables($variables, $connection);
        }

        $variables['types'] = $this->getAvailableConnections();
        $variables['connection'] = $connection;
        $variables['fullPageForm'] = true;

        return $this->renderTemplate(static::TEMPLATE_UPSERT, $variables);
    }

    /**
     * @return array
     * @throws \yii\base\InvalidConfigException
     */
    private function getAvailableConnections()
    {
        $classes = HubSpot::getInstance()->getConnections()->getTypes();

        $connections = [];
        foreach ($classes as $connection) {
            if (!$connection instanceof SavableConnectionInterface) {
                $connection = Instance::ensure($connection, SavableConnectionInterface::class);
            }

            $connections[get_class($connection)] = $connection;
        }

        return $connections;
    }

    /*******************************************
     * BASE PATHS
     *******************************************/

    /**
     * @return string
     */
    protected function getBaseCpPath(): string
    {
        return parent::getBaseCpPath() . '/connections';
    }

    /**
     * @return string
     */
    protected function getBaseActionPath(): string
    {
        return parent::getBaseActionPath() . '/connections';
    }

    /*******************************************
     * UPDATE VARIABLES
     *******************************************/

    /**
     * @param array $variables
     * @param Connection $connection
     */
    protected function updateVariables(array &$variables, Connection $connection)
    {
        $this->baseVariables($variables);
        $variables['title'] .= ' - ' . Craft::t('hubspot', 'Edit') . ' ' . $connection->handle;
        $variables['continueEditingUrl'] = $this->getBaseContinueEditingUrl('/' . $connection->getId());
        $variables['crumbs'][] = [
            'label' => $connection->handle,
            'url' => UrlHelper::url($variables['continueEditingUrl'])
        ];
    }
}
