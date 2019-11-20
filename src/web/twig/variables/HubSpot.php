<?php

/**
 * @copyright  Copyright (c) Flipbox Digital Limited
 * @license    https://flipboxfactory.com/software/hubspot/license
 * @link       https://www.flipboxfactory.com/software/hubspot/
 */

namespace flipbox\craft\hubspot\web\twig\variables;

use Craft;
use flipbox\craft\hubspot\HubSpot as HubSpotPlugin;
use flipbox\craft\hubspot\models\Settings;
use flipbox\craft\hubspot\services\Cache;
use flipbox\craft\hubspot\services\Connections;
use yii\di\ServiceLocator;
use yii\helpers\Json;

/**
 * @author Flipbox Factory <hello@flipboxfactory.com>
 * @since 1.0.0
 */
class HubSpot extends ServiceLocator
{
    use VisitorTrait;

    /**
     * @inheritdoc
     */
    public function init()
    {
        parent::init();

        $this->setComponents([
            'cache' => HubSpotPlugin::getInstance()->getCache(),
            'connections' => HubSpotPlugin::getInstance()->getConnections(),
            'criteria' => Criteria::class
        ]);
    }

    /**
     * Sub-Variables that are accessed 'craft.hubspot.settings'
     *
     * @return Settings
     */
    public function getSettings()
    {
        return HubSpotPlugin::getInstance()->getSettings();
    }

    /**
     * @noinspection PhpDocMissingThrowsInspection
     * @return Cache
     */
    public function getCache(): Cache
    {
        /** @noinspection PhpUnhandledExceptionInspection */
        /** @noinspection PhpIncompatibleReturnTypeInspection */
        return $this->get('cache');
    }

    /**
     * @noinspection PhpDocMissingThrowsInspection
     * @return Connections
     */
    public function getConnections(): Connections
    {
        /** @noinspection PhpUnhandledExceptionInspection */
        /** @noinspection PhpIncompatibleReturnTypeInspection */
        return $this->get('connections');
    }
}
