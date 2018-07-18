<?php

/**
 * @copyright  Copyright (c) Flipbox Digital Limited
 * @license    https://flipboxfactory.com/software/hubspot/license
 * @link       https://www.flipboxfactory.com/software/hubspot/
 */

namespace flipbox\hubspot\cp\actions\fields;

use flipbox\craft\integration\actions\fields\PerformItemAction as PerformItemActionIntegration;
use flipbox\craft\integration\services\IntegrationField;
use flipbox\hubspot\HubSpot;
use flipbox\hubspot\services\ObjectsField;

/**
 * Performs an action on an individual field row
 *
 * @author Flipbox Factory <hello@flipboxfactory.com>
 * @since 1.0.0
 */
class PerformItemAction extends PerformItemActionIntegration
{
    /**
     * @inheritdoc
     * @return ObjectsField
     */
    protected function fieldService(): IntegrationField
    {
        return HubSpot::getInstance()->getObjectsField();
    }
}
