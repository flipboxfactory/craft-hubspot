<?php

/**
 * @copyright  Copyright (c) Flipbox Digital Limited
 * @license    https://flipboxfactory.com/software/hubspot/license
 * @link       https://www.flipboxfactory.com/software/hubspot/
 */

namespace flipbox\hubspot\db;

use flipbox\craft\integration\db\IntegrationAssociationQuery;
use flipbox\hubspot\HubSpot;
use flipbox\hubspot\records\ObjectAssociation;

/**
 * @author Flipbox Factory <hello@flipboxfactory.com>
 * @since 1.0.0
 *
 * @method ObjectAssociation[] all()
 * @method ObjectAssociation one()
 * @method ObjectAssociation[] getCachedResult()
 */
class ObjectAssociationQuery extends IntegrationAssociationQuery
{
    /**
     * @inheritdoc
     * @throws /\Throwable
     */
    public function __construct($modelClass, $config = [])
    {
        HubSpot::getInstance()->getObjectAssociations()->ensureTableExists();
        parent::__construct($modelClass, $config);
    }
}
