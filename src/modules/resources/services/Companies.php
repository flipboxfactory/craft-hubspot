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
        return HubSpot::getInstance()->http()->getCompanies()->create(
            $this->transformToArray($data, $transformer),
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
        $response = HubSpot::getInstance()->http()->getCompanies()->getById(
            $id,
            $authenticationStrategy,
            $cacheStrategy
        );

        if ($response === null) {
            return null;
        }

        return $this->transformToObject($response, $transformer);
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
        // Get contact
        $response = HubSpot::getInstance()->http()->getCompanies()->getByDomain(
            $domain,
            $authenticationStrategy,
            $cacheStrategy
        );

        if ($response === null) {
            return null;
        }

        return $this->transformToObject($response, $transformer);
    }

    /**
     * @param array                         $contact
     * @param callable|TransformerInterface $transformer
     * @return mixed
     */
    public function transformToObject(array $contact, callable $transformer)
    {
        return Factory::item(
            $transformer,
            $contact
        );
    }

    /**
     * @param Component                     $component
     * @param callable|TransformerInterface $transformer
     * @return array
     */
    public function transformToArray(Component $component, callable $transformer): array
    {
        return Factory::item(
            $transformer,
            $component
        );
    }
}
