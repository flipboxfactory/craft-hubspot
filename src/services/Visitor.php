<?php

/**
 * @copyright  Copyright (c) Flipbox Digital Limited
 * @license    https://flipboxfactory.com/software/hubspot/license
 * @link       https://www.flipboxfactory.com/software/hubspot/
 */

namespace flipbox\craft\hubspot\services;

use Craft;
use craft\helpers\Json;
use flipbox\craft\hubspot\HubSpot;
use flipbox\craft\hubspot\queue\SaveVisitor;
use flipbox\craft\hubspot\records\Visitor as VisitorRecord;
use yii\base\Component;

/**
 * @author Flipbox Factory <hello@flipboxfactory.com>
 * @since 1.1.0
 */
class Visitor extends Component
{
    const COOKIE_NAME = 'hubspotutk';

    /**
     * @return string|null
     */
    protected function findTokenValue()
    {
        return $_COOKIE[self::COOKIE_NAME] ?? null;
    }

    /**
     * @return array|null
     */
    public function findContact()
    {
        if (null === ($token = $this->findTokenValue())) {
            HubSpot::info("Visitor token was not found.");
            return null;
        }

        $record = VisitorRecord::findOrCreate($token);
        if ($record->status !== VisitorRecord::STATUS_SUCCESSFUL) {
            $this->syncVisitorIfNecessary($record);
            return null;
        }

        return Json::decodeIfJson(
            $record->contact
        );
    }

    /**
     * @param VisitorRecord $record
     * @return void
     */
    protected function syncVisitorIfNecessary(VisitorRecord $record)
    {
        if ($record->getIsNewRecord()) {
            $record->save();

            Craft::$app->getQueue()->push(
                new SaveVisitor([
                    'token' => $record->token
                ])
            );

            HubSpot::info("Visitor record is new, adding to queue.");
            return;
        }

        HubSpot::info(sprintf(
            "Visitor record status is '%s' and is not 'completed'.",
            $record->status
        ));
    }
}
