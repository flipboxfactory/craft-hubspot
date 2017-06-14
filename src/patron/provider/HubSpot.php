<?php

namespace flipbox\hubspot\patron\provider;

use Craft;
use craft\helpers\ArrayHelper;
use Flipbox\OAuth2\Client\Provider\HubSpot as HubSpotProvider;
use flipbox\patron\modules\configuration\models\Provider;
use flipbox\patron\modules\configuration\providers\AbstractProvider;

class HubSpot extends AbstractProvider
{

    const TEMPLATE_PATH = 'hubspot' . DIRECTORY_SEPARATOR . '_patron' . DIRECTORY_SEPARATOR . 'provider' . DIRECTORY_SEPARATOR . 'settings';

    /**
     * @return string
     */
    public function className(): string
    {
        return HubSpotProvider::class;
    }

    /**
     * @inheritdoc
     */
    public function displayName(): string
    {
        return Craft::t('hubspot', "HubSpot");
    }

    /**
     * @inheritdoc
     */
    public function getHtml(Provider $provider): string
    {
        return Craft::$app->getView()->renderTemplate(
            self::TEMPLATE_PATH,
            [
                'defaultScopes' => ArrayHelper::getValue($provider->getSettings(), 'defaultScopes', [])
            ]
        );
    }

}
