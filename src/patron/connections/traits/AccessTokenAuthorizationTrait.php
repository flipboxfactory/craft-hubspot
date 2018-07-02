<?php

/**
 * @copyright  Copyright (c) Flipbox Digital Limited
 * @license    https://flipboxfactory.com/software/hubspot/license
 * @link       https://www.flipboxfactory.com/software/hubspot/
 */

namespace flipbox\hubspot\patron\connections\traits;

use craft\helpers\ArrayHelper;
use craft\helpers\StringHelper;
use flipbox\patron\Patron;
use Flipbox\Skeleton\Helpers\JsonHelper;
use League\OAuth2\Client\Token\AccessToken;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;

/**
 * @author Flipbox Factory <hello@flipboxfactory.com>
 * @since 1.0.0
 */
trait AccessTokenAuthorizationTrait
{
    use AccessTokenTrait;

    /**
     * @inheritdoc
     * @throws \flipbox\ember\exceptions\NotFoundException
     * @throws \yii\base\InvalidConfigException
     */
    public function prepareAuthorizationRequest(
        RequestInterface $request
    ): RequestInterface {

        foreach ($this->getProvider()->getHeaders() as $key => $value) {
            $request = $request->withAddedHeader($key, $value);
        }

        return $this->addAuthorizationHeader($request);
    }

    /**
     * Add an Authorization Bearer header to the request
     *
     * @param RequestInterface $request
     * @return RequestInterface
     * @throws \flipbox\ember\exceptions\NotFoundException
     * @throws \yii\base\InvalidConfigException
     */
    protected function addAuthorizationHeader(RequestInterface $request): RequestInterface
    {
        return $request->withHeader(
            'Authorization',
            'Bearer ' . (string)$this->getAccessToken()
        );
    }

    /**
     * @inheritdoc
     * @throws \flipbox\ember\exceptions\NotFoundException
     * @throws \yii\base\InvalidConfigException
     */
    public function handleAuthorizationResponse(
        ResponseInterface $response,
        RequestInterface $request,
        callable $runner
    ): ResponseInterface {

        if ($this->responseIsExpiredToken($response)) {
            $response = $this->refreshAndRetry(
                $request,
                $response,
                $runner
            );
        }

        return $response;
    }

    /**
     * @param ResponseInterface $response
     * @return bool
     */
    protected function responseIsExpiredToken(ResponseInterface $response): bool
    {
        if ($response->getStatusCode() !== 401) {
            return false;
        }

        $data = JsonHelper::decodeIfJson(
            $response->getBody()->getContents()
        );

        return $this->hasSessionExpired($data);
    }

    /**
     * @param RequestInterface $request
     * @param ResponseInterface $response
     * @param callable|null $next
     * @return mixed
     * @throws \flipbox\ember\exceptions\NotFoundException
     * @throws \yii\base\InvalidConfigException
     */
    protected function refreshAndRetry(RequestInterface $request, ResponseInterface $response, callable $next = null)
    {
        $refreshToken = $this->getProvider()->getAccessToken('refresh_token', [
            'refresh_token' => $this->getAccessToken()->getRefreshToken()
        ]);

        $this->saveRefreshToken(
            $this->getAccessToken(),
            $refreshToken
        );

        $this->setAccessToken($refreshToken);

        return $next(
            $this->addAuthorizationHeader($request),
            $response
        );
    }

    /**
     * @param AccessToken $accessToken
     * @param AccessToken $refreshToken
     * @return bool
     * @throws \flipbox\ember\exceptions\NotFoundException
     */
    protected function saveRefreshToken(AccessToken $accessToken, AccessToken $refreshToken): bool
    {
        $model = Patron::getInstance()->manageTokens()->get($accessToken);
        $model->accessToken = $refreshToken->getToken();
        return $model->save();
    }


    /**
     * @param array $error
     *
     * This is an example of the error
     * [
     * 'status' => 'error',
     * 'message' => 'This oauth-token (TOKEN_STRING) is expired! expiresAt: 1530045454360, now: 1530114086081',
     * 'correlationId' => '1a45b07b-e8ba-41d5-8a85-508f1fdd3246',
     * 'requestId' => '302880bcbf2c8727850c536b70254015'
     * ]
     *
     * @return bool
     */
    private function hasSessionExpired(array $error): bool
    {
        $message = $error['message'] ?? '';

        // Look for these parts in the message
        $messageParts = [
            'This oauth-token',
            'is expired'
        ];

        if (ArrayHelper::getValue($error, 'status') === 'error' &&
            StringHelper::containsAll($message, $messageParts)
        ) {
            return true;
        }

        return false;
    }
}
