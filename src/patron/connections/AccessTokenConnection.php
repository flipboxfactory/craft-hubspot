<?php

/**
 * @copyright  Copyright (c) Flipbox Digital Limited
 * @license    https://flipboxfactory.com/software/hubspot/license
 * @link       https://www.flipboxfactory.com/software/hubspot/
 */

namespace flipbox\hubspot\patron\connections;

use flipbox\hubspot\connections\IntegrationConnectionInterface;
use Flipbox\OAuth2\Client\Provider\HubSpotResourceOwner;
use yii\base\BaseObject;

/**
 * @author Flipbox Factory <hello@flipboxfactory.com>
 * @since 1.0.0
 */
class AccessTokenConnection extends BaseObject implements IntegrationConnectionInterface
{
    use traits\AccessTokenAuthorizationTrait;

    /**
     * @var string
     */
    private $hubId;

    /**
     * @var string
     */
    private $appId;

    /**
     * @var HubSpotResourceOwner
     */
    private $resourceOwner;

    /**
     * @inheritdoc
     * @throws \flipbox\ember\exceptions\NotFoundException
     * @throws \yii\base\InvalidConfigException
     */
    public function getHubId(): string
    {
        if ($this->hubId === null) {
            if (null === ($hubId = $this->getFromValues('hubId'))) {
                $hubId = $this->getResourceOwner()->getHubId();
            }

            $this->hubId = $hubId ? (string)$hubId : null;
        }

        return $this->hubId;
    }

    /**
     * @inheritdoc
     * @throws \flipbox\ember\exceptions\NotFoundException
     * @throws \yii\base\InvalidConfigException
     */
    public function getAppId(): string
    {
        if ($this->appId === null) {
            if (null === ($appId = $this->getFromValues('appId'))) {
                $appId = $this->getResourceOwner()->getAppId();
            }

            $this->appId = $appId ? (string)$appId : null;
        }

        return $this->appId;
    }

    /**
     * @param string $attribute
     * @return string|null
     * @throws \flipbox\ember\exceptions\NotFoundException
     * @throws \yii\base\InvalidConfigException
     */
    protected function getFromValues(string $attribute)
    {
        $values = $this->getAccessToken()->getValues();
        $value = $values[$attribute] ?? null;
        return $value ? (string)$value : null;
    }

    /**
     * @return HubSpotResourceOwner
     * @throws \flipbox\ember\exceptions\NotFoundException
     * @throws \yii\base\InvalidConfigException
     */
    protected function getResourceOwner()
    {
        if ($this->resourceOwner === null) {
            $this->resourceOwner = $this->getProvider()->getResourceOwner($this->getAccessToken());
        }

        return $this->resourceOwner;
    }
}
