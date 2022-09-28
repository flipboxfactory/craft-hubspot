<?php

/**
 * @copyright  Copyright (c) Flipbox Digital Limited
 * @license    https://flipboxfactory.com/software/hubspot/license
 * @link       https://www.flipboxfactory.com/software/hubspot/
 */

namespace flipbox\craft\hubspot\connections;

use Craft;
use flipbox\craft\integration\connections\AbstractSaveableConnection;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;

/**
 * @author Flipbox Factory <hello@flipboxfactory.com>
 * @since 1.0.0
 */
class PrivateKeyConnection extends AbstractSaveableConnection implements SavableConnectionInterface
{
    /**
     * @var string
     */
    public $key;

    /**
     * @var string
     */
    public $hubId;

    /**
     * Returns the display name of this class.
     *
     * @return string The display name of this class.
     */
    public static function displayName(): string
    {
        return 'Private Application Key';
    }

    /**
     * @inheritdoc
     * @throws \Twig_Error_Loader
     * @throws \yii\base\Exception
     */
    public function getSettingsHtml()
    {
        return Craft::$app->getView()->renderTemplate(
            'hubspot/_components/connections/privateApplicationKey',
            [
                'connection' => $this
            ]
        );
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return array_merge(
            parent::rules(),
            [
                [
                    [
                        'key',
                        'hubId'
                    ],
                    'required'
                ],
                [
                    [
                        'key',
                        'hubId'
                    ],
                    'safe',
                    'on' => [
                        static::SCENARIO_DEFAULT
                    ]
                ]
            ]
        );
    }

    /**
     * @return string
     */
    public function getHubId(): string
    {
        return Craft::parseEnv($this->hubId);
    }

    /**
     * @return string
     */
    public function getKey(): string
    {
        return Craft::parseEnv($this->key);
    }

    /**
     * Add the 'hapikey' to the query
     *
     * @inheritdoc
     */
    public function prepareAuthorizationRequest(
        RequestInterface $request
    ): RequestInterface
    {
        return $request->withAddedHeader('Authorization', "Bearer {$this->getKey()}");
    }

    /**
     * We can't do much, just return the response
     *
     * @inheritdoc
     */
    public function handleAuthorizationResponse(
        ResponseInterface $response,
        RequestInterface  $request,
        callable          $callable
    ): ResponseInterface
    {
        return $response;
    }
}
