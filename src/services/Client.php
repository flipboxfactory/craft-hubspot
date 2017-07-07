<?php

namespace flipbox\hubspot\services;

use flipbox\hubspot\HubSpot;
use GuzzleHttp\Client as GuzzleClient;
use yii\base\Component;

class Client extends Component
{
    /**
     * @param array $config
     * @return GuzzleClient
     */
    public function createApplication(array $config = []): GuzzleClient
    {
        $config['base_uri'] = 'https://api.hubapi.com/';

        // Add API Key to query
        $config['query']['hapikey'] = HubSpot::getInstance()->getSettings()->apiKey;

        return new GuzzleClient($config);
    }
}
