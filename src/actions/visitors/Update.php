<?php

/**
 * @copyright  Copyright (c) Flipbox Digital Limited
 * @license    https://flipboxfactory.com/software/hubspot/license
 * @link       https://www.flipboxfactory.com/software/hubspot/
 */

namespace flipbox\craft\hubspot\actions\visitors;

use Craft;
use flipbox\craft\ember\actions\models\UpdateModel;
use flipbox\craft\hubspot\HubSpot;
use flipbox\craft\hubspot\records\Visitor;
use yii\base\Model;

/**
 * @author Flipbox Factory <hello@flipboxfactory.com>
 * @since 1.1.0
 */
class Update extends UpdateModel
{
    /**
     * @var array
     */
    public $validBodyParams = [
        'status'
    ];

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
     */
    protected function performAction(Model $record): bool
    {
        if (!$record->save()) {
            return false;
        }

        if ($record->status == Visitor::STATUS_PENDING && Craft::$app->getRequest()->getBodyParam('queue')) {
            HubSpot::getInstance()->getVisitor()->syncVisitor($record);
        }

        return true;
    }
}
