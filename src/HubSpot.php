<?php

/**
 * @copyright  Copyright (c) Flipbox Digital Limited
 * @license    https://flipboxfactory.com/software/hubspot/license
 * @link       https://www.flipboxfactory.com/software/hubspot/
 */

namespace flipbox\hubspot;

use Craft;
use craft\base\Plugin;
use craft\events\RegisterComponentTypesEvent;
use craft\events\RegisterTemplateRootsEvent;
use craft\services\Fields;
use craft\services\Plugins;
use craft\web\View;
use flipbox\craft\psr3\Logger;
use flipbox\ember\modules\LoggerTrait;
use flipbox\hubspot\fields\Objects;
use flipbox\hubspot\models\Settings as SettingsModel;
use flipbox\hubspot\patron\Events;
use yii\base\Event;

/**
 * @author Flipbox Factory <hello@flipboxfactory.com>
 * @since 1.0.0
 *
 * @method SettingsModel getSettings()
 *
 * @property services\Cache $cache
 * @property services\Connections $connections
 * @property Logger $psr3Logger
 * @property services\Resources $resources
 * @property services\ObjectAssociations $objectAssociations
 * @property services\ObjectsField $objectsField
 * @property services\Transformers $transformers
 */
class HubSpot extends Plugin
{
    use LoggerTrait;

    /**
     * The default transformer
     */
    const DEFAULT_TRANSFORMER = 'hubspot';

    /**
     * @inheritdoc
     */
    protected static function getLogFileName(): string
    {
        return 'hubspot';
    }

    /**
     * @inheritdoc
     */
    protected static function isDebugModeEnabled()
    {
        return (bool)static::getInstance()->getSettings()->debugMode;
    }

    /**
     * @inheritdoc
     */
    public function init()
    {
        parent::init();

        // Components
        $this->setComponents([
            'cache' => services\Cache::class,
            'connections' => services\Connections::class,
            'psr3Logger' => function () {
                return Craft::createObject([
                    'class' => Logger::class,
                    'logger' => static::getLogger(),
                    'category' => self::getLogFileName()
                ]);
            },
            'resources' => services\Resources::class,
            'objectAssociations' => services\ObjectAssociations::class,
            'objectsField' => services\ObjectsField::class,
            'transformers' => services\Transformers::class,
        ]);

        // Modules
        $this->setModules([
            'cp' => cp\Cp::class

        ]);

        // Integration template directory
        Event::on(
            View::class,
            View::EVENT_REGISTER_CP_TEMPLATE_ROOTS,
            function (RegisterTemplateRootsEvent $e) {
                $e->roots['flipbox/integration'] = Craft::$app->getPath()->getVendorPath() .
                    '/flipboxfactory/craft-integration/src/templates';
            }
        );

        // Register our field types
        Event::on(
            Fields::class,
            Fields::EVENT_REGISTER_FIELD_TYPES,
            function (RegisterComponentTypesEvent $event) {
                $event->types[] = Objects::class;
            }
        );

        // Patron Access Token (if installed)
        Event::on(
            Plugins::class,
            Plugins::EVENT_AFTER_LOAD_PLUGINS,
            function () {
                if (Craft::$app->getPlugins()->getPlugin('patron')) {
                    Events::register();
                }
            }
        );
    }

    /**
     * @inheritdoc
     * @return SettingsModel
     */
    public function createSettingsModel()
    {
        return new SettingsModel();
    }

    /**
     * @inheritdoc
     * @throws \Twig_Error_Loader
     * @throws \yii\base\Exception
     */
    public function settingsHtml()
    {
        return Craft::$app->getView()->renderTemplate('hubspot/settings', [
            'hubspot' => $this
        ]);
    }

    /*******************************************
     * SERVICES
     *******************************************/

    /**
     * @noinspection PhpDocMissingThrowsInspection
     * @return services\Cache
     */
    public function getCache(): services\Cache
    {
        /** @noinspection PhpUnhandledExceptionInspection */
        /** @noinspection PhpIncompatibleReturnTypeInspection */
        return $this->get('cache');
    }

    /**
     * @noinspection PhpDocMissingThrowsInspection
     * @return services\Connections
     */
    public function getConnections(): services\Connections
    {
        /** @noinspection PhpUnhandledExceptionInspection */
        /** @noinspection PhpIncompatibleReturnTypeInspection */
        return $this->get('connections');
    }

    /**
     * @noinspection PhpDocMissingThrowsInspection
     * @return services\ObjectAssociations
     */
    public function getObjectAssociations(): services\ObjectAssociations
    {
        /** @noinspection PhpUnhandledExceptionInspection */
        /** @noinspection PhpIncompatibleReturnTypeInspection */
        return $this->get('objectAssociations');
    }

    /**
     * @noinspection PhpDocMissingThrowsInspection
     * @return services\ObjectsField
     */
    public function getObjectsField(): services\ObjectsField
    {
        /** @noinspection PhpUnhandledExceptionInspection */
        /** @noinspection PhpIncompatibleReturnTypeInspection */
        return $this->get('objectsField');
    }

    /**
     * @noinspection PhpDocMissingThrowsInspection
     * @return Logger
     */
    public function getPsrLogger(): Logger
    {
        /** @noinspection PhpUnhandledExceptionInspection */
        /** @noinspection PhpIncompatibleReturnTypeInspection */
        return $this->get('psr3Logger');
    }

    /**
     * @noinspection PhpDocMissingThrowsInspection
     * @return services\Transformers
     */
    public function getTransformers(): services\Transformers
    {
        /** @noinspection PhpUnhandledExceptionInspection */
        /** @noinspection PhpIncompatibleReturnTypeInspection */
        return $this->get('transformers');
    }


    /*******************************************
     * RESOURCES
     *******************************************/

    /**
     * @noinspection PhpDocMissingThrowsInspection
     * @return services\Resources
     */
    public function getResources(): services\Resources
    {
        /** @noinspection PhpUnhandledExceptionInspection */
        /** @noinspection PhpIncompatibleReturnTypeInspection */
        return $this->get('resources');
    }
}
