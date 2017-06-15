<?php

namespace flipbox\hubspot\modules\resources\services;

use flipbox\hubspot\authentication\AuthenticationStrategyInterface;
use flipbox\hubspot\cache\CacheStrategyInterface;
use flipbox\hubspot\HubSpot;
use Flipbox\Transform\Factory;
use Flipbox\Transform\Transformers\TransformerInterface;

class ContactLists extends AbstractResource
{
    /**
     * @param int $id
     * @param callable|TransformerInterface $transformer
     * @param AuthenticationStrategyInterface|null $authenticationStrategy
     * @param CacheStrategyInterface|null $cacheStrategy
     * @return mixed|null
     */
    public function getById(
        int $id,
        callable $transformer,
        AuthenticationStrategyInterface $authenticationStrategy = null,
        CacheStrategyInterface $cacheStrategy = null
    ) {
        // Get contact
        $response = HubSpot::getInstance()->http()->contactLists()->getById(
            $id,
            $authenticationStrategy,
            $cacheStrategy
        );

        if ($response === null) {
            return null;
        }

        return Factory::item($transformer, $response);
    }

    /**
     * @param int $id
     * @param callable|TransformerInterface $transformer
     * @param AuthenticationStrategyInterface|null $authenticationStrategy
     * @param CacheStrategyInterface|null $cacheStrategy
     * @return mixed|null
     */
    public function getContacts(
        int $id,
        callable $transformer,
        AuthenticationStrategyInterface $authenticationStrategy = null,
        CacheStrategyInterface $cacheStrategy = null
    ) {
        // Get contact
        $response = HubSpot::getInstance()->http()->contactLists()->getContacts(
            $id,
            $authenticationStrategy,
            $cacheStrategy
        );

        if ($response === null) {
            return null;
        }

        return Factory::collection(
            $transformer,
            $response
        );
    }

    /**
     * @param int $id
     * @param array $vids
     * @param array $emails
     * @param AuthenticationStrategyInterface|null $authenticationStrategy
     * @return bool
     */
    public function add(
        int $id,
        array $vids = [],
        array $emails = [],
        AuthenticationStrategyInterface $authenticationStrategy = null
    ) {
        $response = HubSpot::getInstance()->http()->contactLists()->add(
            $id,
            $vids,
            $emails,
            $authenticationStrategy
        );

        if ($response === null) {
            return true;
        }

        return false;
    }

    /**
     * @param int $id
     * @param array $vids
     * @param array $emails
     * @param AuthenticationStrategyInterface|null $authenticationStrategy
     * @return bool
     */
    public function remove(
        int $id,
        array $vids = [],
        array $emails = [],
        AuthenticationStrategyInterface $authenticationStrategy = null
    ) {
        $response = HubSpot::getInstance()->http()->contactLists()->remove(
            $id,
            $vids,
            $emails,
            $authenticationStrategy
        );

        if ($response === null) {
            return true;
        }

        return false;
    }
}
