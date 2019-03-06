<?php

/**
 * @copyright  Copyright (c) Flipbox Digital Limited
 * @license    https://flipboxfactory.com/software/hubspot/license
 * @link       https://www.flipboxfactory.com/software/hubspot/
 */

namespace flipbox\craft\hubspot\actions\connections;

use flipbox\craft\hubspot\records\Connection;
use flipbox\craft\integration\actions\connections\CreateConnection as CreateIntegrationConnection;
use yii\db\ActiveRecord;

/**
 * @author Flipbox Factory <hello@flipboxfactory.com>
 * @since 1.0.0
 */
class CreateConnection extends CreateIntegrationConnection
{
    use PopulateConnectionTrait;

    /**
     * @inheritdoc
     */
    protected function newRecord(array $config = []): ActiveRecord
    {
        $record = new Connection();
        $record->setAttributes($config);
        return $record;
    }

    /**
     * @param Connection $record
     * @inheritdoc
     */
    protected function populate(ActiveRecord $record): ActiveRecord
    {
        parent::populate($record);
        return $this->populateSettings($record);
    }
}
