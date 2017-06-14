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
     * @return array|bool
     */
    public function create(
        $data,
        callable $transformer,
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
        callable $transformer,
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
        callable $transformer,
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
