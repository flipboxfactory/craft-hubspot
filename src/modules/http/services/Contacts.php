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
use Psr\Http\Message\ResponseInterface;

class Contacts extends AbstractResource
{
    /**
     * @param array                                $properties
     * @param AuthenticationStrategyInterface|null $authenticationStrategy
     * @return ResponseInterface
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

        return $segments->run();
    }

    /**
     * @param string                               $email
     * @param array                                $properties
     * @param AuthenticationStrategyInterface|null $authenticationStrategy
     * @return ResponseInterface
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

        return $segments->run();
    }

    /**
     * @param int                                  $id
     * @param array                                $properties
     * @param AuthenticationStrategyInterface|null $authenticationStrategy
     * @return ResponseInterface
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

        return $segments->run();
    }

    /**
     * @param int                                  $id
     * @param AuthenticationStrategyInterface|null $authenticationStrategy
     * @param CacheStrategyInterface|null          $cacheStrategy
     * @return ResponseInterface
     */
    public function getById(
        int $id,
        AuthenticationStrategyInterface $authenticationStrategy = null,
        CacheStrategyInterface $cacheStrategy = null
    ) {
        // Create runner segments
        $segments = new GetById(
            [
            'id' => $id,
            'logger' => $this->getLogger(),
            'cache' => $this->resolveCacheStrategy($cacheStrategy)->getPool()
            ]
        );

        // Prepend authorization
        $this->prependAuthenticationMiddleware(
            $segments,
            $authenticationStrategy
        );

        return $segments->run();
    }

    /**
     * @param string                               $email
     * @param AuthenticationStrategyInterface|null $authenticationStrategy
     * @param CacheStrategyInterface|null          $cacheStrategy
     * @return ResponseInterface
     */
    public function getByEmail(
        string $email,
        AuthenticationStrategyInterface $authenticationStrategy = null,
        CacheStrategyInterface $cacheStrategy = null
    ) {
        // Create runner segments
        $segments = new GetByEmail(
            [
            'email' => $email,
            'logger' => $this->getLogger(),
            'cache' => $this->resolveCacheStrategy($cacheStrategy)->getPool()
            ]
        );

        // Prepend authorization
        $this->prependAuthenticationMiddleware(
            $segments,
            $authenticationStrategy
        );

        return $segments->run();
    }
}
