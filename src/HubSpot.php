<?php

namespace flipbox\hubspot;

use Craft;
use craft\base\Plugin;
use craft\helpers\UrlHelper;
use craft\web\Request;
use flipbox\craft\psr6\Cache;
use flipbox\craft\psr6\events\RegisterCachePools;
use flipbox\hubspot\models\Settings as SettingsModel;
use flipbox\hubspot\patron\provider\HubSpot as HubSpotProvider;
use flipbox\patron\modules\configuration\events\RegisterProviders;
use flipbox\patron\modules\configuration\Module as PatronConfiguration;
use yii\base\Event;

/**
 * @method SettingsModel getSettings()
 */
class HubSpot extends Plugin
{

    /**
     * The default transformer
     */
    const DEFAULT_TRANSFORMER = 'hubspot';

    /**
     * @inheritdoc
     */
    public function init()
    {
        // CP Requests
        if (Craft::$app->getRequest() instanceof Request 
            && Craft::$app->getRequest()->getIsCpRequest()
        ) {

            Event::on(
                PatronConfiguration::class,
                PatronConfiguration::EVENT_REGISTER_PROVIDERS,
                function (RegisterProviders $event) {
                    $event->providers[] = HubSpotProvider::class;
                }
            );

        }

        // Register cache
        Event::on(
            get_class(Craft::$app),
            Cache::EVENT_REGISTER_CACHE_POOLS,
            function (RegisterCachePools $event) {
                $event->addPool('hubspot', 'foo');
            }
        );

        parent::init();
    }

    /**
     * @return SettingsModel
     */
    public function createSettingsModel()
    {
        return new SettingsModel();
    }

    /**
     * @inheritdoc
     */
    public function getSettingsResponse()
    {
        Craft::$app->getResponse()->redirect(
            UrlHelper::cpUrl('hubspot/configuration')
        );

        Craft::$app->end();
    }

    /*******************************************
     * SERVICES
     *******************************************/

    /**
     * @return services\Client
     */
    public function getClient()
    {
        return $this->get('client');
    }

    /**
     * @return \flipbox\craft\psr6\Cache
     */
    public function getCache()
    {
        return $this->get('cache');
    }

    /**
     * @return \flipbox\craft\psr3\Logger
     */
    public function getLogger()
    {
        return $this->get('logger');
    }


    /*******************************************
     * SUB-MODULES
     *******************************************/

    /**
     * @return modules\http\Module
     */
    public function getHttp()
    {
        return $this->getModule('http');
    }

    /**
     * @return modules\resources\Module
     */
    public function getResources()
    {
        return $this->getModule('resources');
    }


    /*******************************************
     * LOGGING
     *******************************************/

    /**
     * Logs an informative message.
     *
     * @param $message
     * @param string  $category
     */
    public static function info($message, $category = 'hubspot')
    {
        Craft::info($message, $category);
    }

    /**
     * Logs a warning message.
     *
     * @param $message
     * @param string  $category
     */
    public static function warning($message, $category = 'hubspot')
    {
        Craft::warning($message, $category);
    }

    /**
     * Logs an error message.
     *
     * @param $message
     * @param string  $category
     */
    public static function error($message, $category = 'hubspot')
    {
        Craft::error($message, $category);
    }
}
