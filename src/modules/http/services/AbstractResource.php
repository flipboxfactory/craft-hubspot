<?php

namespace flipbox\hubspot\modules\http\services;

use flipbox\hubspot\authentication\AuthenticationStrategyInterface;
use flipbox\hubspot\cache\CacheStrategyInterface;
use flipbox\hubspot\HubSpot;
use Flipbox\Relay\Segments\SegmentInterface;
use yii\base\Component;

class AbstractResource extends Component
{
    /**
     * @param AuthenticationStrategyInterface|null $authenticationStrategy
     * @return AuthenticationStrategyInterface
     */
    protected function resolveAuthenticationStrategy(AuthenticationStrategyInterface $authenticationStrategy = null
    ): AuthenticationStrategyInterface {
        if ($authenticationStrategy === null) {
            return HubSpot::getInstance()->getSettings()->getAuthenticationStrategy();
        }

        return $authenticationStrategy;
    }

    /**
     * @param SegmentInterface                     $segments
     * @param AuthenticationStrategyInterface|null $authenticationStrategy
     */
    protected function prependAuthenticationMiddleware(
        SegmentInterface $segments,
        AuthenticationStrategyInterface $authenticationStrategy = null
    ) {
        // The authentication strategy
        $authenticationStrategy = $this->resolveAuthenticationStrategy($authenticationStrategy);

        $segments->addBefore('auth', $authenticationStrategy->getMiddleware());
    }

    /**
     * @param CacheStrategyInterface|null $cacheStrategy
     * @return CacheStrategyInterface
     */
    protected function resolveCacheStrategy(CacheStrategyInterface $cacheStrategy = null): CacheStrategyInterface
    {
        if ($cacheStrategy === null) {
            return HubSpot::getInstance()->getSettings()->getCacheStrategy();
        }

        return $cacheStrategy;
    }

    /**
     * @return \Psr\Log\LoggerInterface
     */
    protected function getLogger()
    {
        return HubSpot::getInstance()->getLogger();
    }
}
