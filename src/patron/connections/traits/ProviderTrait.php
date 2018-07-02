<?php

/**
 * @copyright  Copyright (c) Flipbox Digital Limited
 * @license    https://flipboxfactory.com/software/hubspot/license
 * @link       https://www.flipboxfactory.com/software/hubspot/
 */

namespace flipbox\hubspot\patron\connections\traits;

use Craft;
use Flipbox\OAuth2\Client\Provider\HubSpot;
use flipbox\patron\Patron;
use yii\base\InvalidArgumentException;

/**
 * @author Flipbox Factory <hello@flipboxfactory.com>
 * @since 1.0.0
 */
trait ProviderTrait
{
    /**
     * @var mixed
     */
    private $provider;

    /**
     * @param $provider
     * @return $this
     */
    public function setProvider($provider)
    {
        $this->provider = $provider;
        return $this;
    }

    /**
     * @return HubSpot
     * @throws \flipbox\ember\exceptions\NotFoundException
     * @throws \yii\base\InvalidConfigException
     */
    public function getProvider(): HubSpot
    {
        if ($this->provider instanceof HubSpot) {
            return $this->provider;
        }

        return $this->provider = $this->resolveProvider($this->provider);
    }

    /**
     * @param $provider
     * @return HubSpot
     * @throws \flipbox\ember\exceptions\NotFoundException
     * @throws \yii\base\InvalidConfigException
     */
    protected function resolveProvider($provider): HubSpot
    {
        if (is_numeric($provider) || is_string($provider)) {
            $provider = Patron::getInstance()->getProviders()->get($provider);
        } else {
            $provider = Craft::createObject($provider);
        }

        if (!$provider instanceof HubSpot) {
            throw new InvalidArgumentException("Unable to resolve provider");
        }

        return $provider;
    }
}
