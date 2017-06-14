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
        $transformer,
        AuthenticationStrategyInterface $authenticationStrategy = null
    ) {
        return HubSpot::getInstance()->getHttp()->getCompanies()->create(
            $this->transformToArray($data, $transformer),
            $authenticationStrategy
        );
    }

    /**
     * @param int                                  $id
     * @param string|callable|TransformerInterface $transformer
     * @param AuthenticationStrategyInterface|null $authenticationStrategy
     * @param CacheStrategyInterface|null          $cacheStrategy
     * @return array|null
     */
    public function getById(
        int $id,
        $transformer = HubSpot::DEFAULT_TRANSFORMER,
        AuthenticationStrategyInterface $authenticationStrategy = null,
        CacheStrategyInterface $cacheStrategy = null
    ) {
        // Get contact
        $response = HubSpot::getInstance()->getHttp()->getCompanies()->getById(
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
     * @param string|callable|TransformerInterface $transformer
     * @param AuthenticationStrategyInterface|null $authenticationStrategy
     * @param CacheStrategyInterface|null          $cacheStrategy
     * @return array|null
     */
    public function getByDomain(
        string $domain,
        $transformer = HubSpot::DEFAULT_TRANSFORMER,
        AuthenticationStrategyInterface $authenticationStrategy = null,
        CacheStrategyInterface $cacheStrategy = null
    ) {
        // Get contact
        $response = HubSpot::getInstance()->getHttp()->getCompanies()->getByDomain(
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
     * @param array                                $contact
     * @param string|callable|TransformerInterface $transformer
     * @return mixed
     */
    private function transformToObject(array $contact, $transformer = HubSpot::DEFAULT_TRANSFORMER)
    {
        return Factory::item(
            $this->resolveTransformer(
                $transformer,
                Organization::class,
                Transformer::CONTEXT_OBJECT
            ),
            $contact
        );
    }

    /**
     * @param User                                 $user
     * @param string|callable|TransformerInterface $transformer
     * @return array
     */
    private function transformToArray(User $user, $transformer = HubSpot::DEFAULT_TRANSFORMER): array
    {
        return Factory::item(
            $this->resolveTransformer(
                $transformer,
                Organization::class,
                Transformer::CONTEXT_ARRAY
            ),
            $user
        );
    }
}
