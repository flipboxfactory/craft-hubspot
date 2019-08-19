<?php

/**
 * @copyright  Copyright (c) Flipbox Digital Limited
 * @license    https://flipboxfactory.com/software/hubspot/license
 * @link       https://www.flipboxfactory.com/software/hubspot/
 */

namespace flipbox\craft\hubspot\records;

use flipbox\craft\ember\records\ActiveRecord;

/**
 * @author Flipbox Factory <hello@flipboxfactory.com>
 * @since 1.1.0
 *
 * @property string $token
 * @property string $contact
 * @property string $status
 */
class Visitor extends ActiveRecord
{
    /**
     * The table alias
     */
    const TABLE_ALIAS = 'hubspot_visitors';

    /**
     * Request to HubSpot has been performed and contact results have been saved.
     */
    const STATUS_SUCCESSFUL = 'successful';

    /**
     * Request to HubSpot has not been made; Likely new and has not been processed.
     */
    const STATUS_PENDING = 'pending';

    /**
     * A HubSpot error was returned; Further investigation is needed.
     */
    const STATUS_ERROR = 'error';

    /**
     * The HubSpot contact could not be found.
     */
    const STATUS_NOT_FOUND = 'not_found';

    /**
     * Set a default status
     *
     * @inheritDoc
     */
    public function init()
    {
        if ($this->status === null) {
            $this->status = static::STATUS_PENDING;
        }
        parent::init();
    }

    /**
     * @param string $identifier
     * @return Visitor
     */
    public static function findOrCreate(string $identifier): Visitor
    {
        $condition = [
            'token' => $identifier
        ];

        if (null === ($record = static::findOne($condition))) {
            $record = new self($condition);
        }

        return $record;
    }
}