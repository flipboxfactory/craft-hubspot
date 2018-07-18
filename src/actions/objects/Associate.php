<?php

/**
 * @copyright  Copyright (c) Flipbox Digital Limited
 * @license    https://flipboxfactory.com/software/hubspot/license
 * @link       https://www.flipboxfactory.com/software/hubspot/
 */

namespace flipbox\hubspot\actions\objects;

use flipbox\craft\integration\actions\objects\Associate as AssociateIntegration;
use flipbox\craft\integration\records\IntegrationAssociation;
use flipbox\craft\integration\services\IntegrationAssociations;
use flipbox\hubspot\fields\Objects;
use flipbox\hubspot\HubSpot;
use flipbox\hubspot\services\ObjectAssociations;
use Psr\Http\Message\ResponseInterface;

/**
 * @author Flipbox Factory <hello@flipboxfactory.com>
 * @since 1.0.0
 */
class Associate extends AssociateIntegration
{
    /**
     * @inheritdoc
     * @return ObjectAssociations
     */
    protected function associationService(): IntegrationAssociations
    {
        return HubSpot::getInstance()->getObjectAssociations();
    }

    /**
     * @inheritdoc
     * @param IntegrationAssociation $record
     */
    protected function validate(
        IntegrationAssociation $record
    ): bool {

        if (null === ($fieldId = $record->fieldId)) {
            return false;
        }

        if (null === ($field = HubSpot::getInstance()->getObjectsField()->findById($fieldId))) {
            return false;
        }

        /** @var Objects $field */

        /** @var ResponseInterface $response */
        $response = $field->getResource()->rawHttpRead(
            $record->objectId,
            $field->getConnection(),
            $field->getCache()
        );

        return $response->getStatusCode() >= 200 && $response->getStatusCode() <= 299;
    }
}
