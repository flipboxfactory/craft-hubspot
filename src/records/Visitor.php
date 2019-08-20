<?php

/**
 * @copyright  Copyright (c) Flipbox Digital Limited
 * @license    https://flipboxfactory.com/software/hubspot/license
 * @link       https://www.flipboxfactory.com/software/hubspot/
 */

namespace flipbox\craft\hubspot\records;

use craft\db\Table;
use flipbox\craft\ember\records\ActiveRecord;
use flipbox\craft\hubspot\queue\SaveVisitor;
use yii\db\Query;

/**
 * @author Flipbox Factory <hello@flipboxfactory.com>
 * @since 1.1.0
 *
 * @property string $token
 * @property array $contact
 * @property string $connection
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
     * @param string $token
     * @param string|null $connection
     * @return Visitor
     */
    public static function findOrCreate(string $token, string $connection = null): Visitor
    {
        $condition = [
            'token' => $token
        ];

        if (null === ($record = static::findOne($condition))) {
            $record = new self($condition);
        }

        return $record;
    }

    /**
     * Identify if there is a job that has yet to be processed.
     * @return bool
     */
    public function inQueue(): bool
    {
        return $this->getQueueJobs()
            ->andWhere([
                'fail' => false,
                'attempt' => null
            ])
            ->exists();
    }

    /**
     * @return Query
     */
    public function getQueueJobs(): Query
    {
        return (new Query())
            ->from(Table::QUEUE)
            ->andWhere([
                'description' => SaveVisitor::DESCRIPTION . $this->token
            ])
            ->orderBy([
                'timePushed' => SORT_DESC
            ]);
    }
}