<?php

/**
 * @copyright  Copyright (c) Flipbox Digital Limited
 * @license    https://flipboxfactory.com/software/hubspot/license
 * @link       https://www.flipboxfactory.com/software/hubspot/
 */

namespace flipbox\hubspot\patron;

use flipbox\hubspot\patron\providers\HubSpotSettings;
use Flipbox\OAuth2\Client\Provider\HubSpot as HubSpotProvider;
use Flipbox\OAuth2\Client\Provider\HubSpotResourceOwner;
use flipbox\patron\cp\Cp;
use flipbox\patron\events\PersistToken;
use flipbox\patron\events\RegisterProviders;
use flipbox\patron\events\RegisterProviderSettings;
use flipbox\patron\services\Tokens;
use yii\base\Event;

/**
 * @author Flipbox Factory <hello@flipboxfactory.com>
 * @since 1.0.0
 */
class Events
{
    /**
     * Register events
     */
    public static function register()
    {
        // OAuth2 Provider
        Event::on(
            Cp::class,
            Cp::EVENT_REGISTER_PROVIDERS,
            function (RegisterProviders $event) {
                $event->providers[] = HubSpotProvider::class;
            }
        );

        // OAuth2 Provider Settings
        RegisterProviderSettings::on(
            HubSpotProvider::class,
            RegisterProviderSettings::REGISTER_SETTINGS,
            function (RegisterProviderSettings $event) {
                $event->class = HubSpotSettings::class;
            }
        );

        Event::on(
            HubSpotProvider::class,
            Tokens::EVENT_BEFORE_PERSIST_TOKEN,
            function (PersistToken $e) {
                $values = $e->record->values;

                if (!isset($values['appId']) || !isset($values['hubId'])) {
                    /** @var HubSpotProvider $provider */
                    $provider = $e->provider;

                    /** @var HubSpotResourceOwner $owner */
                    $owner = $provider->getResourceOwner($e->token);

                    $values['hubId'] = $owner->getHubId();
                    $values['appId'] = $owner->getAppId();

                    $e->record->values = $values;
                }
            }
        );
    }
}
