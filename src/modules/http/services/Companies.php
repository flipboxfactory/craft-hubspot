<?php

namespace flipbox\hubspot\modules\http\services;

use Craft;
use craft\helpers\Json;
use flipbox\hubspot\authentication\AuthenticationStrategyInterface;
use flipbox\hubspot\cache\CacheStrategyInterface;
use flipbox\hubspot\HubSpot;
use Flipbox\Relay\HubSpot\Segment\Companies\Create;
use Flipbox\Relay\HubSpot\Segment\Companies\GetByDomain;
use Flipbox\Relay\HubSpot\Segment\Companies\GetById;
use Flipbox\Relay\HubSpot\Segment\Companies\UpdateById;
use Flipbox\Relay\HubSpot\Segment\Companies\UpdateByDomain;

class Companies extends AbstractResource
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
            HubSpot::warning(
                Craft::t(
                    "Unable to create company: {properties}",
                    ['{properties}' => Json::encode($properties)]
                )
            );
            return Json::decodeIfJson($response->getBody()->getContents());
        }

        return true;
    }

    /**
     * @param int                                  $id
     * @param array                                $properties
     * @param AuthenticationStrategyInterface|null $authenticationStrategy
     * @return array|null
     */
    public function updateById(
        int $id,
        array $properties,
        AuthenticationStrategyInterface $authenticationStrategy = null
    ) {
        // Create runner segments
        $segments = new UpdateById([
            'id' => $id,
            'properties' => $properties,
            'logger' => $this->getLogger()
        ]);

        // Prepend authorization
        $this->prependAuthenticationMiddleware(
            $segments,
            $authenticationStrategy
        );

        // Run Http
        $response = $segments->run();

        if ($response->getStatusCode() !== 200) {
            HubSpot::warning(
                Craft::t(
                    "Unable to update company with id: {id}",
                    ['{id}' => $id]
                )
            );
            return null;
        }

        return Json::decodeIfJson($response->getBody()->getContents());
    }

    /**
     * @param string                               $domain
     * @param array                                $properties
     * @param AuthenticationStrategyInterface|null $authenticationStrategy
     * @return array|null
     */
    public function updateByDomain(
        string $domain,
        array $properties,
        AuthenticationStrategyInterface $authenticationStrategy = null
    ) {
        // Create runner segments
        $segments = new UpdateByDomain([
            'domain' => $domain,
            'properties' => $properties,
            'logger' => $this->getLogger()
        ]);

        // Prepend authorization
        $this->prependAuthenticationMiddleware(
            $segments,
            $authenticationStrategy
        );

        // Run Http
        $response = $segments->run();

        if ($response->getStatusCode() !== 200) {
            HubSpot::warning(
                Craft::t(
                    "Unable to update company with domain: {domain}",
                    ['{domain}' => $domain]
                )
            );
            return null;
        }

        return Json::decodeIfJson($response->getBody()->getContents());
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
        // Create runner segments
        $segments = new GetById([
            'id' => $id,
            'logger' => $this->getLogger(),
            'cache' => $this->resolveCacheStrategy($cacheStrategy)->getPool()
        ]);

        // Prepend authorization
        $this->prependAuthenticationMiddleware(
            $segments,
            $authenticationStrategy
        );

        // Run Http
        $response = $segments->run();

        if ($response->getStatusCode() !== 200) {
            HubSpot::warning(
                Craft::t(
                    "Unable to get company with id: {id}",
                    ['{id}' => $id]
                )
            );
            return null;
        }

        return Json::decodeIfJson($response->getBody()->getContents());
    }

    /**
     * @param string                               $domain
     * @param AuthenticationStrategyInterface|null $authenticationStrategy
     * @param CacheStrategyInterface|null          $cacheStrategy
     * @return array|null
     */
    public function getByDomain(
        string $domain,
        AuthenticationStrategyInterface $authenticationStrategy = null,
        CacheStrategyInterface $cacheStrategy = null
    ) {
        // Create runner segments
        $segments = new GetByDomain([
            'domain' => $domain,
            'logger' => $this->getLogger(),
            'cache' => $this->resolveCacheStrategy($cacheStrategy)->getPool()
        ]);

        // Prepend authorization
        $this->prependAuthenticationMiddleware(
            $segments,
            $authenticationStrategy
        );

        // Run Http
        $response = $segments->run();

        if ($response->getStatusCode() !== 200) {
            HubSpot::warning(
                Craft::t(
                    "Unable to get company with domain: {domain}",
                    [
                    '{domain}' => $domain
                    ]
                )
            );
            return null;
        }

        return Json::decodeIfJson($response->getBody()->getContents());
    }
}
