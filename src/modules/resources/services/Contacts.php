<?php

namespace flipbox\hubspot\modules\resources\services;

use craft\elements\User;
use flipbox\hubspot\authentication\AuthenticationStrategyInterface;
use flipbox\hubspot\cache\CacheStrategyInterface;
use flipbox\hubspot\HubSpot;
use Flipbox\Transform\Factory;
use Flipbox\Transform\Transformers\TransformerInterface;
use flipbox\transformer\Transformer;

class Contacts extends AbstractResource
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
        return HubSpot::getInstance()->getHttp()->getContacts()->create(
            $this->transformToArray($data, $transformer),
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
        $transformer,
        AuthenticationStrategyInterface $authenticationStrategy = null
    ) {
        return HubSpot::getInstance()->getHttp()->getContacts()->updateByEmail(
            $email,
            $this->transformToArray($data, $transformer),
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
        $transformer,
        AuthenticationStrategyInterface $authenticationStrategy = null
    ) {
        return HubSpot::getInstance()->getHttp()->getContacts()->updateById(
            $id,
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
        $contact = HubSpot::getInstance()->getHttp()->getContacts()->getById(
            $id,
            $authenticationStrategy,
            $cacheStrategy
        );

        if ($contact === null) {
            return null;
        }

        return $this->transformToObject($contact, $transformer);
    }

    /**
     * @param string                               $email
     * @param string|callable|TransformerInterface $transformer
     * @param AuthenticationStrategyInterface|null $authenticationStrategy
     * @param CacheStrategyInterface|null          $cacheStrategy
     * @return array|null
     */
    public function getByEmail(
        string $email,
        $transformer = HubSpot::DEFAULT_TRANSFORMER,
        AuthenticationStrategyInterface $authenticationStrategy = null,
        CacheStrategyInterface $cacheStrategy = null
    ) {
        // Get contact
        $contact = HubSpot::getInstance()->getHttp()->getContacts()->getByEmail(
            $email,
            $authenticationStrategy,
            $cacheStrategy
        );

        if ($contact === null) {
            return null;
        }

        return $this->transformToObject($contact, $transformer);
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
                User::class,
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
                User::class,
                Transformer::CONTEXT_ARRAY
            ),
            $user
        );
    }
}
