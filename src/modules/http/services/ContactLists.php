<?php

namespace flipbox\hubspot\modules\http\services;

use craft\helpers\Json;
use flipbox\hubspot\authentication\AuthenticationStrategyInterface;
use flipbox\hubspot\cache\CacheStrategyInterface;
use flipbox\hubspot\HubSpot;
use Flipbox\Relay\HubSpot\Segment\ContactLists\Add;
use Flipbox\Relay\HubSpot\Segment\ContactLists\Remove;
use Flipbox\Relay\HubSpot\Segment\ContactLists\GetById;
use Flipbox\Relay\HubSpot\Segment\ContactLists\GetContacts;

class ContactLists extends AbstractResource
{

    /**
     * @param int                                  $id
     * @param AuthenticationStrategyInterface|null $authenticationStrategy
     * @param CacheStrategyInterface|null          $cacheStrategy
     * @return array|null
     */
    public function getContacts(
        int $id,
        AuthenticationStrategyInterface $authenticationStrategy = null,
        CacheStrategyInterface $cacheStrategy = null
    ) {
        // Create runner segments
        $segments = new GetContacts(
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

        // Run Http
        $response = $segments->run();

        if ($response->getStatusCode() !== 200) {
            HubSpot::warning(
                sprintf(
                    "Unable to get contact list with id:  %s",
                    $id
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

        // Run Http
        $response = $segments->run();

        if ($response->getStatusCode() !== 200) {
            HubSpot::warning(
                sprintf(
                    "Unable to get contact list with id:  %s",
                    $id
                )
            );
            return null;
        }

        return Json::decodeIfJson($response->getBody()->getContents());
    }

    /**
     * @param int                                  $id
     * @param array                                $vids
     * @param array                                $emails
     * @param AuthenticationStrategyInterface|null $authenticationStrategy
     * @return array|null
     */
    public function add(
        int $id,
        array $vids = [],
        array $emails = [],
        AuthenticationStrategyInterface $authenticationStrategy = null
    ) {
        // Create runner segments
        $segments = new Add(
            [
                'id' => $id,
                'vids' => $vids,
                'emails' => $emails,
                'logger' => $this->getLogger()]
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
                    "Unable to get contact list with id:  %s",
                    $id
                )
            );
            return null;
        }

        return Json::decodeIfJson($response->getBody()->getContents());
    }

    /**
     * @param int                                  $id
     * @param array                                $vids
     * @param array                                $emails
     * @param AuthenticationStrategyInterface|null $authenticationStrategy
     * @return array|null
     */
    public function remove(
        int $id,
        array $vids = [],
        array $emails = [],
        AuthenticationStrategyInterface $authenticationStrategy = null
    ) {
        // Create runner segments
        $segments = new Remove(
            [
                'id' => $id,
                'vids' => $vids,
                'emails' => $emails,
                'logger' => $this->getLogger()]
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
                    "Unable to get contact list with id:  %s",
                    $id
                )
            );
            return null;
        }

        return Json::decodeIfJson($response->getBody()->getContents());
    }
}
