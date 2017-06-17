<?php

namespace flipbox\hubspot\modules\resources\services;

use craft\helpers\Json;
use flipbox\hubspot\authentication\AuthenticationStrategyInterface;
use flipbox\hubspot\cache\CacheStrategyInterface;
use flipbox\hubspot\HubSpot;
use Flipbox\Transform\Factory;
use Flipbox\Transform\Transformers\TransformerInterface;

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
        $payload = Factory::item($transformer, $data);

        $response = HubSpot::getInstance()->http()->companies()->create(
            $payload,
            $authenticationStrategy
        );

        // Interpret response
        if ($response->getStatusCode() !== 200) {
            $body = Json::decodeIfJson($response->getBody()->getContents());
            HubSpot::warning(
                sprintf(
                    "Unable to create company: %s, errors: %s",
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

        $response = HubSpot::getInstance()->http()->companies()->updateById(
            $id,
            $payload,
            $authenticationStrategy
        );

        if ($response->getStatusCode() !== 200) {
            $body = Json::decodeIfJson($response->getBody()->getContents());
            HubSpot::warning(
                sprintf(
                    "Unable to update company with id %s: %s, errors: %s",
                    $id,
                    Json::encode($payload),
                    Json::encode($body)
                )
            );
            return null;
        }

        return Json::decodeIfJson($response->getBody()->getContents());
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
        $payload = Factory::item($transformer, $data);

        $response = HubSpot::getInstance()->http()->companies()->updateByDomain(
            $domain,
            Factory::item($transformer, $data),
            $authenticationStrategy
        );

        if ($response->getStatusCode() !== 200) {
            $body = Json::decodeIfJson($response->getBody()->getContents());
            HubSpot::warning(
                sprintf(
                    "Unable to update company with domain %s: %s, errors: %s",
                    $domain,
                    Json::encode($payload),
                    Json::encode($body)
                )
            );
            return null;
        }

        return Json::decodeIfJson($response->getBody()->getContents());
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

        if ($response->getStatusCode() !== 200) {
            $body = Json::decodeIfJson($response->getBody()->getContents());
            HubSpot::warning(
                sprintf(
                    "Unable to get company with id %s, errors: %s",
                    $id,
                    Json::encode($body)
                )
            );
            return null;
        }

        return Factory::item(
            $transformer,
            Json::decodeIfJson($response->getBody()->getContents())
        );
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

        if ($response->getStatusCode() !== 200) {
            $body = Json::decodeIfJson($response->getBody()->getContents());
            HubSpot::warning(
                sprintf(
                    "Unable to get company with domain %s, errors: %s",
                    $domain,
                    Json::encode($body)
                )
            );
            return null;
        }

        return Factory::item(
            $transformer,
            Json::decodeIfJson($response->getBody()->getContents())
        );
    }
}
