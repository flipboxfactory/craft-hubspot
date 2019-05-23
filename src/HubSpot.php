<?php

/**
 * @copyright  Copyright (c) Flipbox Digital Limited
 * @license    https://flipboxfactory.com/software/hubspot/license
 * @link       https://www.flipboxfactory.com/software/hubspot/
 */

namespace flipbox\craft\hubspot;

use Craft;
use craft\base\Plugin;
use craft\events\RegisterComponentTypesEvent;
use craft\events\RegisterTemplateRootsEvent;
use craft\events\RegisterUrlRulesEvent;
use craft\helpers\UrlHelper;
use craft\services\Fields;
use craft\web\twig\variables\CraftVariable;
use craft\web\UrlManager;
use craft\web\View;
use flipbox\craft\ember\modules\LoggerTrait;
use flipbox\craft\hubspot\fields\Companies;
use flipbox\craft\hubspot\fields\ContactLists;
use flipbox\craft\hubspot\fields\Contacts;
use flipbox\craft\hubspot\models\Settings as SettingsModel;
use flipbox\craft\hubspot\records\ObjectAssociation;
use flipbox\craft\hubspot\web\twig\variables\HubSpot as HubSpotVariable;
use flipbox\craft\psr3\Logger;
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
 */
class HubSpot extends Plugin
{
    use LoggerTrait;

    /**
     * @var string
     */
    public static $category = 'hubspot';

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
                    'category' => static::$category
                ]);
            }
        ]);

        // Modules
        $this->setModules([
            'cp' => cp\Cp::class

        ]);

        \Flipbox\HubSpot\HubSpot::setLogger(
            $this->getPsrLogger()
        );

        // Template variables
        Event::on(
            CraftVariable::class,
            CraftVariable::EVENT_INIT,
            function (Event $event) {
                /** @var CraftVariable $variable */
                $variable = $event->sender;
                $variable->set('hubspot', HubSpotVariable::class);
            }
        );

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
                $event->types[] = Companies::class;
                $event->types[] = ContactLists::class;
                $event->types[] = Contacts::class;
            }
        );

        // CP routes
        Event::on(
            UrlManager::class,
            UrlManager::EVENT_REGISTER_CP_URL_RULES,
            [self::class, 'onRegisterCpUrlRules']
        );

        // Make sure we have an objects table
        if ($this->isInstalled) {
            ObjectAssociation::ensureEnvironmentTableExists();
        }
    }

    /**
     * @inheritdoc
     */
    public function getCpNavItem()
    {
        return array_merge(
            parent::getCpNavItem(),
            [
                'subnav' => [
                    'hubspot.limits' => [
                        'label' => static::t('Limits'),
                        'url' => 'hubspot/limits',
                    ],
                    'hubspot.settings' => [
                        'label' => static::t('Settings'),
                        'url' => 'hubspot/settings',
                    ]
                ]
            ]
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
     */
    public function settingsHtml()
    {
        Craft::$app->getResponse()->redirect(
            UrlHelper::cpUrl('hubspot/settings')
        );

        Craft::$app->end();
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
     * @return Logger
     */
    public function getPsrLogger(): Logger
    {
        /** @noinspection PhpUnhandledExceptionInspection */
        /** @noinspection PhpIncompatibleReturnTypeInspection */
        return $this->get('psr3Logger');
    }


    /*******************************************
     * TRANSLATE
     *******************************************/

    /**
     * Translates a message to the specified language.
     *
     * This is a shortcut method of [[\Craft::t()]].
     *     *
     * @param string $message the message to be translated.
     * @param array $params the parameters that will be used to replace the corresponding placeholders in the message.
     * @param string $language the language code (e.g. `en-US`, `en`). If this is null, the current
     * [[\yii\base\Application::language|application language]] will be used.
     * @return string the translated message.
     */
    public static function t($message, $params = [], $language = null)
    {
        return Craft::t('hubspot', $message, $params, $language);
    }


    /*******************************************
     * MODULES
     *******************************************/

    /**
     * @noinspection PhpDocMissingThrowsInspection
     * @return cp\Cp
     */
    public function getCp(): cp\Cp
    {
        /** @noinspection PhpUnhandledExceptionInspection */
        /** @noinspection PhpIncompatibleReturnTypeInspection */
        return $this->getModule('cp');
    }


    /*******************************************
     * EVENTS
     *******************************************/

    /**
     * @param RegisterUrlRulesEvent $event
     */
    public static function onRegisterCpUrlRules(RegisterUrlRulesEvent $event)
    {
        $event->rules = array_merge(
            $event->rules,
            [
                // ??
                'hubspot' => 'hubspot/cp/settings/view/general/index',

                // LIMITS
                'hubspot/limits' => 'hubspot/cp/view/limits/index',

                // OBJECTS: PAYLOAD
                'hubspot/objects/payloads/<field:\d+>/element/<element:\d+>' => 'hubspot/cp/view/object-payloads/index',

                // SETTINGS
                'hubspot/settings' => 'hubspot/cp/settings/view/general/index',

                // SETTINGS: CONNECTIONS
                'hubspot/settings/connections' => 'hubspot/cp/settings/view/connections/index',
                'hubspot/settings/connections/new' => 'hubspot/cp/settings/view/connections/upsert',
                'hubspot/settings/connections/<identifier:\d+>' => 'hubspot/cp/settings/view/connections/upsert',

            ]
        );
    }
}
