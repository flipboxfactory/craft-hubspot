<?php

/**
 * @copyright  Copyright (c) Flipbox Digital Limited
 * @license    https://flipboxfactory.com/software/hubspot/license
 * @link       https://www.flipboxfactory.com/software/hubspot/
 */

namespace flipbox\craft\hubspot\web\twig\variables;

use flipbox\craft\hubspot\HubSpot as HubSpotPlugin;
use yii\helpers\Json;

/**
 * @author Flipbox Factory <hello@flipboxfactory.com>
 * @since 1.0.0
 */
trait VisitorTrait
{
    /**
     * @noinspection PhpDocMissingThrowsInspection
     * @param bool $toQueue
     * @param string|null $connection
     * @param string|null $cache
     * @return array|null
     */
    public function getVisitor(bool $toQueue = true, string $connection = null, string $cache = null)
    {
        try {
            return HubSpotPlugin::getInstance()->getVisitor()->findContact($toQueue, $connection, $cache);
        } catch (\Exception $e) {
            HubSpotPlugin::warning(
                sprintf(
                    "Exception caught while trying to get HubSpot Visitor. Exception: [%s].",
                    (string)Json::encode([
                        'Trace' => $e->getTraceAsString(),
                        'File' => $e->getFile(),
                        'Line' => $e->getLine(),
                        'Code' => $e->getCode(),
                        'Message' => $e->getMessage()
                    ])
                ),
                __METHOD__
            );
            return null;
        }
    }
}
