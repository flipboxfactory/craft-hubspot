<?php

/**
 * @copyright  Copyright (c) Flipbox Digital Limited
 * @license    https://flipboxfactory.com/software/hubspot/license
 * @link       https://www.flipboxfactory.com/software/hubspot/
 */

namespace flipbox\craft\hubspot\services;

use flipbox\craft\hubspot\HubSpot;
use flipbox\craft\integration\services\IntegrationCache;

/**
 * @author Flipbox Factory <hello@flipboxfactory.com>
 * @since 1.0.0
 */
class Cache extends IntegrationCache
{
    /**
     * The override file
     */
    public $overrideFile = 'hubspot-cache';

    /**
     * @inheritdoc
     */
    protected function getDefaultCache(): string
    {
        return HubSpot::getInstance()->getSettings()->getDefaultCache();
    }

    /**
     * @inheritdoc
     */
    protected function handleCacheNotFound(string $handle)
    {
        HubSpot::warning(sprintf(
            "Unable to find cache '%s'.",
            $handle
        ));

        return parent::handleCacheNotFound($handle);
    }
}
