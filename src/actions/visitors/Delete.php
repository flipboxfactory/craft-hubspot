<?php

/**
 * @copyright  Copyright (c) Flipbox Digital Limited
 * @license    https://flipboxfactory.com/software/hubspot/license
 * @link       https://www.flipboxfactory.com/software/hubspot/
 */

namespace flipbox\craft\hubspot\actions\visitors;

use flipbox\craft\ember\actions\models\DeleteModel;
use flipbox\craft\hubspot\records\Visitor;
use yii\base\Model;

/**
 * @author Flipbox Factory <hello@flipboxfactory.com>
 * @since 1.1.0
 */
class Delete extends DeleteModel
{
    /**
     * @param int|string $identifier
     * @return Visitor|mixed|null
     */
    protected function find($identifier)
    {
        return Visitor::findOne($identifier);
    }

    /**
     * @inheritDoc
     * @param Visitor $record
     * @throws \Throwable
     * @throws \yii\db\StaleObjectException
     */
    protected function performAction(Model $record): bool
    {
        return $record->delete();
    }
}
