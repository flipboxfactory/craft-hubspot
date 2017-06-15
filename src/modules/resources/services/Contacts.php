<?php

namespace flipbox\hubspot\modules\resources\services;

use craft\elements\User;
use flipbox\hubspot\authentication\AuthenticationStrategyInterface;
use flipbox\hubspot\cache\CacheStrategyInterface;
use flipbox\hubspot\HubSpot;
use Flipbox\Transform\Factory;
use Flipbox\Transform\Transformers\TransformerInterface;
use flipbox\transformer\Transformer;
use yii\base\Component;

class Contacts extends AbstractResource
{

    /**
     * @param $data
     * @param callable|TransformerInterface        $transformer
     * @param AuthenticationStrategyInterface|null $authenticationStrategy
     * @return array
     */
    public function create(
        $data,
        callable $transformer,
        AuthenticationStrategyInterface $authenticationStrategy = null
    ) {
        return HubSpot::getInstance()->http()->contacts()->create(
            Factory::item($transformer, $data),
            $authenticationStrategy
        );
    }

    /**
     * @param string                               $email
     * @param $data
     * @param callable|TransformerInterface        $transformer
     * @param AuthenticationStrategyInterface|null $authenticationStrategy
     * @return bool|array
     */
    public function updateByEmail(
        string $email,
        $data,
        callable $transformer,
        AuthenticationStrategyInterface $authenticationStrategy = null
    ) {
        return HubSpot::getInstance()->http()->contacts()->updateByEmail(
            $email,
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
        return HubSpot::getInstance()->http()->contacts()->updateById(
            $id,
            Factory::item($transformer, $data),
            $authenticationStrategy
        );
    }

    /**
     * @param int                                  $id
     * @param callable $transformer
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
        $response = HubSpot::getInstance()->http()->contacts()->getById(
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
     * @param string                               $email
     * @param callable|TransformerInterface $transformer
     * @param AuthenticationStrategyInterface|null $authenticationStrategy
     * @param CacheStrategyInterface|null          $cacheStrategy
     * @return array|null
     */
    public function getByEmail(
        string $email,
        callable $transformer,
        AuthenticationStrategyInterface $authenticationStrategy = null,
        CacheStrategyInterface $cacheStrategy = null
    ) {
        // Get contact
        $response = HubSpot::getInstance()->http()->contacts()->getByEmail(
            $email,
            $authenticationStrategy,
            $cacheStrategy
        );

        if ($response === null) {
            return null;
        }

        return Factory::item($transformer, $response);
    }
}
