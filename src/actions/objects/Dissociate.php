<?php

/**
 * @copyright  Copyright (c) Flipbox Digital Limited
 * @license    https://flipboxfactory.com/software/hubspot/license
 * @link       https://www.flipboxfactory.com/software/hubspot/
 */

namespace flipbox\hubspot\actions\objects;

use flipbox\craft\integration\actions\objects\Dissociate as DissociateIntegration;
use flipbox\craft\integration\services\IntegrationAssociations;
use flipbox\hubspot\HubSpot;
use flipbox\hubspot\services\ObjectAssociations;

/**
 * @author Flipbox Factory <hello@flipboxfactory.com>
 * @since 1.0.0
 */
class Dissociate extends DissociateIntegration
{
    /**
     * @inheritdoc
     * @return ObjectAssociations
     */
    protected function associationService(): IntegrationAssociations
    {
        return HubSpot::getInstance()->getObjectAssociations();
    }
}
