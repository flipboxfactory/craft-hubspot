<?php

/**
 * @copyright  Copyright (c) Flipbox Digital Limited
 * @license    https://flipboxfactory.com/software/hubspot/license
 * @link       https://www.flipboxfactory.com/software/hubspot/
 */

namespace flipbox\hubspot\models;

use craft\base\Model;
use flipbox\ember\helpers\ModelHelper;
use flipbox\hubspot\services\Connections;
use yii\caching\Dependency;

/**
 * @author Flipbox Factory <hello@flipboxfactory.com>
 * @since 1.0.0
 */
class Settings extends Model
{
    /**
     * @var bool
     */
    public $debugMode = false;

    /**
     * @var string
     */
    public $environmentTablePostfix = '';

    /**
     * @var int|null|false
     */
    public $associationsCacheDuration = false;

    /**
     * @var null|Dependency
     */
    public $associationsCacheDependency = null;

    /**
     * @var string
     */
    private $defaultConnection = Connections::APP_CONNECTION;

    /**
     * @var string
     */
    private $defaultIntegrationConnection = Connections::INTEGRATION_CONNECTION;

    /**
     * @var string
     */
    private $defaultCache = Connections::APP_CONNECTION;

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
     * @return array
     */
    public function attributes()
    {
        return array_merge(
            parent::attributes(),
            [
                'defaultConnection',
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
                        'defaultCache'
                    ],
                    'safe',
                    'on' => [
                        ModelHelper::SCENARIO_DEFAULT
                    ]
                ]
            ]
        );
    }
}
