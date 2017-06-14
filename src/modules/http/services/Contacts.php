<?php

namespace flipbox\hubspot\modules\http\services;

use craft\helpers\Json;
use flipbox\hubspot\authentication\AuthenticationStrategyInterface;
use flipbox\hubspot\cache\CacheStrategyInterface;
use flipbox\hubspot\HubSpot;
use Flipbox\Relay\HubSpot\Segment\Contacts\Create;
use Flipbox\Relay\HubSpot\Segment\Contacts\GetByEmail;
use Flipbox\Relay\HubSpot\Segment\Contacts\GetById;
use Flipbox\Relay\HubSpot\Segment\Contacts\UpdateByEmail;
use Flipbox\Relay\HubSpot\Segment\Contacts\UpdateById;

class Contacts extends AbstractResource
{
    /**
     * @param array                                $properties
     * @param AuthenticationStrategyInterface|null $authenticationStrategy
     * @return bool|array
     */
    public function create(
        array $properties,
        AuthenticationStrategyInterface $authenticationStrategy = null
    ) {
        // Create runner segments
        $segments = new Create(
            [
            'properties' => $properties,
            'logger' => $this->getLogger()
            ]
        );

        // Prepend authorization
        $this->prependAuthenticationMiddleware(
            $segments,
            $authenticationStrategy
        );

        // Run Http
        $response = $segments->run();

        // Interpret response
        if ($response->getStatusCode() !== 201) {

            $body = Json::decodeIfJson($response->getBody()->getContents());

            HubSpot::warning(
                sprintf(
                    "Unable to create user: %s, errors: %s",
                    Json::encode($properties),
                    Json::encode($body)
                )
            );
            return $body;
        }

        return true;
    }

    /**
     * @param string                               $email
     * @param array                                $properties
     * @param AuthenticationStrategyInterface|null $authenticationStrategy
     * @return bool|array
     */
    public function updateByEmail(
        string $email,
        array $properties,
        AuthenticationStrategyInterface $authenticationStrategy = null
    ) {
        // Create runner segments
        $segments = new UpdateByEmail(
            [
            'email' => $email,
            'properties' => $properties,
            'logger' => $this->getLogger()
            ]
        );

        // Prepend authorization
        $this->prependAuthenticationMiddleware(
            $segments,
            $authenticationStrategy
        );

        // Run Http
        $response = $segments->run();

        // Interpret response
        if ($response->getStatusCode() !== 204) {
            $body = Json::decodeIfJson($response->getBody()->getContents());

            HubSpot::warning(
                sprintf(
                    "Unable to create user email: %s, errors: %s",
                    $email,
                    Json::encode($properties),
                    Json::encode($body)
                )
            );
            return $body;
        }

        return true;
    }

    /**
     * @param int                                  $id
     * @param array                                $properties
     * @param AuthenticationStrategyInterface|null $authenticationStrategy
     * @return bool|array
     */
    public function updateById(
        int $id,
        array $properties,
        AuthenticationStrategyInterface $authenticationStrategy = null
    ) {
        // Create runner segments
        $segments = new UpdateById(
            [
            'id' => $id,
            'properties' => $properties,
            'logger' => $this->getLogger()
            ]
        );

        // Prepend authorization
        $this->prependAuthenticationMiddleware(
            $segments,
            $authenticationStrategy
        );

        // Run Http
        $response = $segments->run();

        // Interpret response
        if ($response->getStatusCode() !== 204) {

            $body = Json::decodeIfJson($response->getBody()->getContents());

            HubSpot::warning(
                sprintf(
                    "Unable to create user id %s: %s, errors: %s",
                    $id,
                    Json::encode($properties),
                    Json::encode($body)
                )
            );
            return $body;
        }

        return true;
    }

    /**
     * @param int                                  $id
     * @param AuthenticationStrategyInterface|null $authenticationStrategy
     * @param CacheStrategyInterface|null          $cacheStrategy
     * @return array|null
     */
    public function getById(
        int $id,
        AuthenticationStrategyInterface $authenticationStrategy = null,
        CacheStrategyInterface $cacheStrategy = null
    ) {
        // The authentication strategy
        $authenticationStrategy = $this->resolveAuthenticationStrategy($authenticationStrategy);

        // The cache strategy
        $cacheStrategy = $this->resolveCacheStrategy($cacheStrategy);

        // Create runner segments
        $segments = new GetById(
            [
            'id' => $id,
            'logger' => $this->getLogger(),
            'cache' => $cacheStrategy->getPool()
            ]
        );

        // Prepend authorization
        $this->prependAuthenticationMiddleware(
            $segments,
            $authenticationStrategy
        );

        // Run Http
        $response = $segments->run();

        if ($response->getStatusCode() !== 200) {
            HubSpot::warning(
                sprintf(
                    "Unable to get user with id:  %s",
                    $id
                )
            );
            return null;
        }

        return Json::decodeIfJson($response->getBody()->getContents());
    }

    /**
     * @param string                               $email
     * @param AuthenticationStrategyInterface|null $authenticationStrategy
     * @param CacheStrategyInterface|null          $cacheStrategy
     * @return array|null
     */
    public function getByEmail(
        string $email,
        AuthenticationStrategyInterface $authenticationStrategy = null,
        CacheStrategyInterface $cacheStrategy = null
    ) {
        // The authentication strategy
        $authenticationStrategy = $this->resolveAuthenticationStrategy($authenticationStrategy);

        // The cache strategy
        $cacheStrategy = $this->resolveCacheStrategy($cacheStrategy);

        // Create runner segments
        $segments = new GetByEmail(
            [
            'email' => $email,
            'logger' => $this->getLogger(),
            'cache' => $cacheStrategy->getPool()
            ]
        );

        // Prepend authorization
        $this->prependAuthenticationMiddleware(
            $segments,
            $authenticationStrategy
        );

        // Run Http
        $response = $segments->run();

        if ($response->getStatusCode() !== 200) {
            HubSpot::warning(
                sprintf(
                    "Unable to get user with email: {email}",
                    $email
                )
            );
            return null;
        }

        return Json::decodeIfJson($response->getBody()->getContents());
    }
}
