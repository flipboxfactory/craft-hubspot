<?php

namespace flipbox\hubspot\modules\resources\services;

use craft\elements\User;
use craft\helpers\Json;
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
        $payload = Factory::item($transformer, $data);

        $response = HubSpot::getInstance()->http()->contacts()->create(
            $payload,
            $authenticationStrategy
        );

        // Interpret response
        if ($response->getStatusCode() !== 200) {
            $body = Json::decodeIfJson($response->getBody()->getContents());

            HubSpot::warning(
                sprintf(
                    "Unable to create user: %s, errors: %s",
                    Json::encode($payload),
                    Json::encode($body)
                )
            );

            return [
                false,
                $body
            ];
        }

        return [
            true,
            Json::decodeIfJson($response->getBody()->getContents())
        ];
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
        $payload = Factory::item($transformer, $data);

        $response = HubSpot::getInstance()->http()->contacts()->updateByEmail(
            $email,
            $payload,
            $authenticationStrategy
        );

        // Interpret response
        if ($response->getStatusCode() !== 204) {
            $body = Json::decodeIfJson($response->getBody()->getContents());

            HubSpot::warning(
                sprintf(
                    "Unable to create user email: %s, errors: %s",
                    $email,
                    Json::encode($payload),
                    Json::encode($body)
                )
            );
            return $body;
        }

        return true;
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
        $payload = Factory::item($transformer, $data);

        $response = HubSpot::getInstance()->http()->contacts()->updateById(
            $id,
            $payload,
            $authenticationStrategy
        );

        // Interpret response
        if ($response->getStatusCode() !== 204) {
            $body = Json::decodeIfJson($response->getBody()->getContents());

            HubSpot::warning(
                sprintf(
                    "Unable to create user id %s: %s, errors: %s",
                    $id,
                    Json::encode($payload),
                    Json::encode($body)
                )
            );
            return $body;
        }

        return true;
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

        if ($response->getStatusCode() !== 200) {
            HubSpot::warning(
                sprintf(
                    "Unable to get user with id:  %s",
                    $id
                )
            );
            return null;
        }

        return Factory::item($transformer, Json::decodeIfJson($response->getBody()->getContents()));
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


        if ($response->getStatusCode() !== 200) {
            HubSpot::warning(
                sprintf(
                    "Unable to get user with email: {email}",
                    $email
                )
            );
            return null;
        }

        return Factory::item($transformer, Json::decodeIfJson($response->getBody()->getContents()));
    }
}
