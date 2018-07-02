<?php

/**
 * @copyright  Copyright (c) Flipbox Digital Limited
 * @license    https://flipboxfactory.com/software/hubspot/license
 * @link       https://www.flipboxfactory.com/software/hubspot/
 */

namespace flipbox\hubspot\web\twig\variables;

use flipbox\hubspot\HubSpot as HubSpotPlugin;
use yii\di\ServiceLocator;

/**
 * @author Flipbox Factory <hello@flipboxfactory.com>
 * @since 1.0.0
 */
class HubSpot extends ServiceLocator
{
    /**
     * @inheritdoc
     */
    public function init()
    {
        parent::init();

        $this->setComponents([
            'cache' => HubSpotPlugin::getInstance()->getCache(),
            'connections' => HubSpotPlugin::getInstance()->getConnections(),
            'resources' => HubSpotPlugin::getInstance()->getResources(),
            'settings' => HubSpotPlugin::getInstance()->getSettings()
        ]);
    }
}
