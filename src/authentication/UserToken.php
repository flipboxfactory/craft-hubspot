<?php

namespace flipbox\hubspot\authentication;

use Craft;
use craft\elements\User;
use flipbox\hubspot\HubSpot;
use flipbox\patron\Patron;
use Flipbox\Relay\HubSpot\Middleware\Authorization\Token;

class UserToken implements AuthenticationStrategyInterface
{
    /**
     * @var int
     */
    public $providerId;

    /**
     * @var int|User
     */
    public $user;

    /**
     * @return array|null
     */
    public function getMiddleware()
    {
        // Resolve OAuth2 Token
        $token = $this->resolveToken();

        if ($token === null) {
            return null;
        }

        return [
            'class' => Token::class,
            'token' => $token,
            'logger' => HubSpot::getInstance()->logger()
        ];
    }

    /**
     * @return \League\OAuth2\Client\Token\AccessToken|null
     */
    protected function resolveToken()
    {
        $userId = $this->resolveUserId();

        if ($userId === null || $this->providerId === null) {
            return null;
        }

        return Patron::getInstance()->getToken()->findByCondition(
            [
            'providerId' => $this->providerId,
            'userId' => $userId
            ]
        );
    }

    /**
     * @return int|null
     */
    protected function resolveUserId()
    {
        $user = $this->user;

        if (is_numeric($user)) {
            return $user;
        }

        if ($user === null) {
            $user = Craft::$app->getUser()->getIdentity();
        }

        if (!$user instanceof User) {
            return null;
        }

        return $user->getId();
    }
}
