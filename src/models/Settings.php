<?php

/**
 * @copyright  Copyright (c) Flipbox Digital Limited
 * @license    https://flipboxfactory.com/software/hubspot/license
 * @link       https://www.flipboxfactory.com/software/hubspot/
 */

namespace flipbox\craft\hubspot\models;

use craft\base\Model;
use flipbox\craft\hubspot\helpers\TransformerHelper;
use flipbox\craft\hubspot\services\Cache;
use flipbox\craft\hubspot\services\Connections;
use flipbox\craft\hubspot\transformers\CreateUpsertPayloadFromElement;
use flipbox\craft\hubspot\transformers\PopulateElementFromResponse;

/**
 * @author Flipbox Factory <hello@flipboxfactory.com>
 * @since 1.0.0
 */
class Settings extends Model
{
    /**
     * @var string
     */
    public $environmentTableSuffix = '';

    /**
     * @var string
     */
    private $defaultCache = Cache::APP_CACHE;

    /**
     * @var string
     */
    private $defaultConnection = Connections::DEFAULT_CONNECTION;

    /**
     * @var string
     */
    private $defaultIntegrationConnection = Connections::DEFAULT_INTEGRATION_CONNECTION;

    /**
     * @param string $key
     * @return $this
     */
    public function setDefaultConnection(string $key)
    {
        $this->defaultConnection = $key;
        return $this;
    }

    /**
     * @return string
     */
    public function getDefaultConnection(): string
    {
        return $this->defaultConnection;
    }

    /**
     * @param string $key
     * @return $this
     */
    public function setDefaultIntegrationConnection(string $key)
    {
        $this->defaultIntegrationConnection = $key;
        return $this;
    }

    /**
     * @return string
     */
    public function getDefaultIntegrationConnection(): string
    {
        return $this->defaultIntegrationConnection;
    }

    /**
     * @param string $key
     * @return $this
     */
    public function setDefaultCache(string $key)
    {
        $this->defaultCache = $key;
        return $this;
    }

    /**
     * @return string
     */
    public function getDefaultCache(): string
    {
        return $this->defaultCache;
    }

    /**
     * @return callable
     */
    public function getSyncUpsertPayloadTransformer(): callable
    {
        return TransformerHelper::resolveTransformer([
            'class' => CreateUpsertPayloadFromElement::class,
            'action' => 'sync'
        ]);
    }

    /**
     * @return callable
     */
    public function getSyncPopulateElementTransformer(): callable
    {
        return TransformerHelper::resolveTransformer([
            'class' => PopulateElementFromResponse::class,
            'action' => 'sync'
        ]);
    }

    /**
     * @return array
     */
    public function attributes()
    {
        return array_merge(
            parent::attributes(),
            [
                'defaultConnection',
                'defaultIntegrationConnection',
                'defaultCache'
            ]
        );
    }

    /**
     * @return array
     */
    public function rules()
    {
        return array_merge(
            parent::rules(),
            [
                [
                    [
                        'defaultConnection',
                        'defaultIntegrationConnection',
                        'defaultCache'
                    ],
                    'safe',
                    'on' => [
                        self::SCENARIO_DEFAULT
                    ]
                ]
            ]
        );
    }
}
