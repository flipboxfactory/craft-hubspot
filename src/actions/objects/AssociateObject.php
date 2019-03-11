<?php

/**
 * @copyright  Copyright (c) Flipbox Digital Limited
 * @license    https://flipboxfactory.com/software/hubspot/license
 * @link       https://www.flipboxfactory.com/software/hubspot/
 */

namespace flipbox\craft\hubspot\actions\objects;

use flipbox\craft\hubspot\fields\ObjectsFieldInterface;
use flipbox\craft\integration\actions\objects\AssociateObject as AssociateIntegration;
use flipbox\craft\integration\records\IntegrationAssociation;
use Psr\Http\Message\ResponseInterface;

/**
 * @author Flipbox Factory <hello@flipboxfactory.com>
 * @since 1.0.0
 */
class AssociateObject extends AssociateIntegration
{
    /**
     * @inheritdoc
     * @param IntegrationAssociation $record
     */
    protected function validate(
        IntegrationAssociation $record
    ): bool {

        $field = $record->getField();

        if (!$field instanceof ObjectsFieldInterface) {
            return false;
        }

        /** @var ResponseInterface $response */
        $response = $field->readFromHubSpot(
            $record->objectId
        );

        return $response->getStatusCode() >= 200 && $response->getStatusCode() <= 299;
    }
}
