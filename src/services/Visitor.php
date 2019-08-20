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
     * @param string|null $connection
     * @return mixed|null
     */
    public function findContact(string $connection = null)
    {
        if (null === ($token = $this->findTokenValue())) {
            HubSpot::info("Visitor token was not found.");
            return null;
        }

        $record = VisitorRecord::findOrCreate($token, $connection);
        if ($record->status !== VisitorRecord::STATUS_SUCCESSFUL) {
            // If new, always queue up sync operation
            if ($record->getIsNewRecord()) {
                $record->save();
            }

            if ($record->status === VisitorRecord::STATUS_PENDING) {
                $this->syncVisitor($record);
            }

            HubSpot::info(sprintf(
                "Visitor record status is '%s' and is not 'completed'; nothing to return.",
                $record->status
            ));
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
    public function syncVisitor(VisitorRecord $record)
    {
        if ($record->inQueue()) {
            HubSpot::warning("Queue Job already exists; ignoring.");
            return;
        }

        Craft::$app->getQueue()->push(
            new SaveVisitor([
                'token' => $record->token,
                'connection' => $record->connection
            ])
        );

        HubSpot::info("Added Queue Job to sync Visitor from HubSpot");
    }
}
