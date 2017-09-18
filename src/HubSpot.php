<?php

namespace flipbox\hubspot;

use Craft;
use craft\base\Plugin;
use craft\events\RegisterComponentTypesEvent;
use craft\services\Fields;
use craft\web\Request;
use flipbox\hubspot\fields\Company;
use flipbox\hubspot\fields\Contact;
use flipbox\hubspot\fields\ContactList;
use flipbox\hubspot\models\Settings as SettingsModel;
use flipbox\hubspot\patron\provider\HubSpot as HubSpotProvider;
use flipbox\patron\modules\configuration\events\RegisterProviders;
use flipbox\patron\modules\configuration\Module as PatronConfiguration;
use flipbox\spark\modules\interfaces\LoggableInterface;
use flipbox\spark\modules\traits\LoggableTrait;
use yii\base\Event;

/**
 * @method SettingsModel getSettings()
 */
class HubSpot extends Plugin implements LoggableInterface
{

    use LoggableTrait;

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
        if (Craft::$app->getRequest() instanceof Request &&
            Craft::$app->getRequest()->getIsCpRequest()
        ) {
            Event::on(
                PatronConfiguration::class,
                PatronConfiguration::EVENT_REGISTER_PROVIDERS,
                function (RegisterProviders $event) {
                    $event->providers[] = HubSpotProvider::class;
                }
            );
        }

        // Register our field types
        Event::on(
            Fields::class,
            Fields::EVENT_REGISTER_FIELD_TYPES,
            function (RegisterComponentTypesEvent $event) {
                $event->types[] = Company::class;
                $event->types[] = Contact::class;
                $event->types[] = ContactList::class;
            }
        );

        // PSR3 logger override
        $this->logger()->logger = static::getLogger();

        parent::init();
    }

    /**
     * @inheritdoc
     */
    public function isDebugModeEnabled()
    {
        return (bool)$this->getSettings()->debugMode;
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
     * @return services\Company
     */
    public function getCompany()
    {
        return $this->get('company');
    }

    /**
     * @return services\Contact
     */
    public function getContact()
    {
        return $this->get('contact');
    }

    /**
     * @return services\ContactList
     */
    public function getContactList()
    {
        return $this->get('contact-list');
    }

    /**
     * @return \flipbox\craft\psr6\Cache
     */
    public function cache()
    {
        return $this->get('cache');
    }

    /**
     * @return services\Client
     */
    public function client()
    {
        return $this->get('client');
    }

    /**
     * @return \flipbox\craft\psr3\Logger
     */
    public function logger()
    {
        return $this->get('logger');
    }

    /**
     * @return services\Transformer
     */
    public function transformer()
    {
        return $this->get('transformer');
    }

    /*******************************************
     * SUB-MODULES
     *******************************************/

    /**
     * @return modules\http\Module
     */
    public function http()
    {
        return $this->getModule('http');
    }

    /**
     * @return modules\resources\Module
     */
    public function resources()
    {
        return $this->getModule('resources');
    }
}
