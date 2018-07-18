<?php

/**
 * @copyright  Copyright (c) Flipbox Digital Limited
 * @license    https://flipboxfactory.com/software/hubspot/license
 * @link       https://www.flipboxfactory.com/software/hubspot/
 */

namespace flipbox\hubspot\cp\actions\fields;

use flipbox\craft\integration\actions\fields\CreateItem as CreateItemIntegration;
use flipbox\craft\integration\services\IntegrationAssociations;
use flipbox\hubspot\HubSpot;
use flipbox\hubspot\services\ObjectAssociations;

/**
 * @author Flipbox Factory <hello@flipboxfactory.com>
 * @since 1.0.0
 */
class CreateItem extends CreateItemIntegration
{
    /**
     * @inheritdoc
     * @return ObjectAssociations
     */
    public function associationService(): IntegrationAssociations
    {
        return HubSpot::getInstance()->getObjectAssociations();
    }
}
