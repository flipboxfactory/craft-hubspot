<?php

/**
 * @copyright  Copyright (c) Flipbox Digital Limited
 * @license    https://flipboxfactory.com/software/hubspot/license
 * @link       https://www.flipboxfactory.com/software/hubspot/
 */

namespace flipbox\craft\hubspot\actions\connections;

use Craft;
use flipbox\craft\hubspot\records\Connection;

/**
 * @author Flipbox Factory <hello@flipboxfactory.com>
 * @since 1.0.0
 */
trait PopulateConnectionTrait
{
    /**
     * @param Connection $record
     * @return Connection
     */
    protected function populateSettings(Connection $record): Connection
    {
        $allSettings = Craft::$app->getRequest()->getBodyParam('settings');

        if (!is_array($allSettings)) {
            $allSettings = [$allSettings];
        }

        $settings = $allSettings[$record->class] ?? null;
        $record->settings = $settings;

        return $record;
    }
}
