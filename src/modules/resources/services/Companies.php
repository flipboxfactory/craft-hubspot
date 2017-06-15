<?php

namespace flipbox\hubspot\modules\resources\services;

use craft\elements\User;
use flipbox\hubspot\authentication\AuthenticationStrategyInterface;
use flipbox\hubspot\cache\CacheStrategyInterface;
use flipbox\hubspot\HubSpot;
use flipbox\organization\elements\Organization;
use Flipbox\Transform\Factory;
use Flipbox\Transform\Transformers\TransformerInterface;
use flipbox\transformer\Transformer;
use yii\base\Component;

class Companies extends AbstractResource
{

    /**
     * @param $data
     * @param callable|TransformerInterface        $transformer
     * @param AuthenticationStrategyInterface|null $authenticationStrategy
     * @return array|bool
     */
    public function create(
        $data,
        callable $transformer,
        AuthenticationStrategyInterface $authenticationStrategy = null
    ) {
        return HubSpot::getInstance()->http()->companies()->create(
            Factory::item($transformer, $data),
            $authenticationStrategy
        );
    }

    /**
     * @param int                                  $id
     * @param $data
     * @param callable|TransformerInterface        $transformer
     * @param AuthenticationStrategyInterface|null $authenticationStrategy
     * @return bool|array
     */
    public function updateById(
        int $id,
        $data,
        callable $transformer,
        AuthenticationStrategyInterface $authenticationStrategy = null
    ) {
        return HubSpot::getInstance()->http()->companies()->updateById(
            $id,
            Factory::item($transformer, $data),
            $authenticationStrategy
        );
    }

    /**
     * @param string                               $domain
     * @param $data
     * @param callable|TransformerInterface        $transformer
     * @param AuthenticationStrategyInterface|null $authenticationStrategy
     * @return bool|array
     */
    public function updateByDomain(
        string $domain,
        $data,
        callable $transformer,
        AuthenticationStrategyInterface $authenticationStrategy = null
    ) {
        return HubSpot::getInstance()->http()->companies()->updateByDomain(
            $domain,
            Factory::item($transformer, $data),
            $authenticationStrategy
        );
    }

    /**
     * @param int                                  $id
     * @param callable|TransformerInterface $transformer
     * @param AuthenticationStrategyInterface|null $authenticationStrategy
     * @param CacheStrategyInterface|null          $cacheStrategy
     * @return array|null
     */
    public function getById(
        int $id,
        callable $transformer,
        AuthenticationStrategyInterface $authenticationStrategy = null,
        CacheStrategyInterface $cacheStrategy = null
    ) {
        // Get contact
        $response = HubSpot::getInstance()->http()->companies()->getById(
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
     * @param string                               $domain
     * @param callable|TransformerInterface $transformer
     * @param AuthenticationStrategyInterface|null $authenticationStrategy
     * @param CacheStrategyInterface|null          $cacheStrategy
     * @return array|null
     */
    public function getByDomain(
        string $domain,
        callable $transformer,
        AuthenticationStrategyInterface $authenticationStrategy = null,
        CacheStrategyInterface $cacheStrategy = null
    ) {
        // Get response
        $response = HubSpot::getInstance()->http()->companies()->getByDomain(
            $domain,
            $authenticationStrategy,
            $cacheStrategy
        );

        if ($response === null) {
            return null;
        }

        return Factory::item($transformer, $response);
    }
}
