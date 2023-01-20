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
use flipbox\craft\hubspot\services\Visitor;
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
    public static $category = 'flipbox-hubspot';

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
            },
            'visitor' => services\Visitor::class
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
                $variable->set(HubSpot::getInstance()->getSettings()->variableKey, HubSpotVariable::class);
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
    public function getCpNavItem(): ?array
    {
        return array_merge(
            parent::getCpNavItem(),
            [
                'subnav' => [
                    'flipbox-hubspot.visitors' => [
                        'label' => static::t('Visitors'),
                        'url' => 'flipbox-hubspot/visitors',
                    ],
                    'flipbox-hubspot.limits' => [
                        'label' => static::t('Limits'),
                        'url' => 'flipbox-hubspot/limits',
                    ],
                    'flipbox-hubspot.settings' => [
                        'label' => static::t('Settings'),
                        'url' => 'flipbox-hubspot/settings',
                    ]
                ]
            ]
        );
    }

    /**
     * @inheritdoc
     * @return SettingsModel
     */
    public function createSettingsModel(): ?\craft\base\Model
    {
        return new SettingsModel();
    }

    /**
     * @inheritdoc
     */
    public function settingsHtml(): ?string
    {
        Craft::$app->getResponse()->redirect(
            UrlHelper::cpUrl('flipbox-hubspot/settings')
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

    /**
     * @noinspection PhpDocMissingThrowsInspection
     * @return Visitor
     */
    public function getVisitor(): Visitor
    {
        /** @noinspection PhpUnhandledExceptionInspection */
        /** @noinspection PhpIncompatibleReturnTypeInspection */
        return $this->get('visitor');
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
        return Craft::t('flipbox-hubspot', $message, $params, $language);
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
                'flipbox-hubspot' => 'flipbox-hubspot/cp/settings/view/general/index',

                // LIMITS
                'flipbox-hubspot/limits' => 'flipbox-hubspot/cp/view/limits/index',

                // VISITORS
                'flipbox-hubspot/visitors' => 'flipbox-hubspot/cp/view/visitors/index',
                'flipbox-hubspot/visitors/<identifier:\d+>' => 'flipbox-hubspot/cp/view/visitors/detail',

                // OBJECTS: PAYLOAD
                'flipbox-hubspot/objects/payloads/<field:\d+>/element/<element:\d+>' => 'flipbox-hubspot/cp/view/object-payloads/index',

                // SETTINGS
                'flipbox-hubspot/settings' => 'flipbox-hubspot/cp/settings/view/general/index',

                // SETTINGS: CONNECTIONS
                'flipbox-hubspot/settings/connections' => 'flipbox-hubspot/cp/settings/view/connections/index',
                'flipbox-hubspot/settings/connections/new' => 'flipbox-hubspot/cp/settings/view/connections/upsert',
                'flipbox-hubspot/settings/connections/<identifier:\d+>' => 'flipbox-hubspot/cp/settings/view/connections/upsert',

            ]
        );
    }
}
