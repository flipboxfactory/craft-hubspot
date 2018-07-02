<?php

/**
 * @copyright  Copyright (c) Flipbox Digital Limited
 * @license    https://flipboxfactory.com/software/hubspot/license
 * @link       https://www.flipboxfactory.com/software/hubspot/
 */

namespace flipbox\hubspot\patron\connections\traits;

use flipbox\patron\Patron;
use League\OAuth2\Client\Token\AccessToken;

/**
 * @author Flipbox Factory <hello@flipboxfactory.com>
 * @since 1.0.0
 */
trait AccessTokenTrait
{
    use ProviderTrait;

    /**
     * @var AccessToken|null
     */
    private $accessToken;

    /**
     * @param AccessToken $accessToken
     */
    public function setAccessToken(AccessToken $accessToken)
    {
        $this->accessToken = $accessToken;
    }

    /**
     * @return AccessToken
     * @throws \flipbox\ember\exceptions\NotFoundException
     * @throws \yii\base\InvalidConfigException
     */
    public function getAccessToken(): AccessToken
    {
        if ($this->accessToken instanceof AccessToken) {
            return $this->accessToken;
        }

        return $this->accessToken = Patron::getInstance()->getTokens()->get($this->getProvider());
    }
}
