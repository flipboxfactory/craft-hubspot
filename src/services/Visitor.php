<?php

/**
 * @copyright  Copyright (c) Flipbox Digital Limited
 * @license    https://flipboxfactory.com/software/hubspot/license
 * @link       https://www.flipboxfactory.com/software/hubspot/
 */

namespace flipbox\craft\hubspot\services;

use Craft;
use craft\helpers\Json;
use flipbox\craft\hubspot\criteria\ContactCriteria;
use flipbox\craft\hubspot\events\PrepVisitorDataFromHubSpotEvent;
use flipbox\craft\hubspot\HubSpot;
use flipbox\craft\hubspot\queue\SaveVisitor;
use flipbox\craft\hubspot\records\Visitor as VisitorRecord;
use yii\base\Component;
use yii\base\Exception;

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
     * @param bool $toQueue
     * @param string|null $connection
     * @return mixed|null
     */
    public function findContact(bool $toQueue = true, string $connection = null)
    {
        if (null === ($token = $this->findTokenValue())) {
            HubSpot::info("Visitor token was not found.");
            return null;
        }

        $record = VisitorRecord::findOrCreate($token, $connection);
        if ($record->status !== VisitorRecord::STATUS_SUCCESSFUL) {
            // If new, save now so we can reference later
            if ($record->getIsNewRecord()) {
                $record->save();
            }

            if ($record->status === VisitorRecord::STATUS_PENDING) {
                $this->syncVisitor($record, $toQueue);
            }

            // It's possible the sync operation was run immediately
            if ($record->status !== VisitorRecord::STATUS_SUCCESSFUL) {
                HubSpot::info(sprintf(
                    "Visitor record status is '%s' and is not 'completed'; nothing to return.",
                    $record->status
                ));
                return null;
            }
        }

        return Json::decodeIfJson(
            $record->contact
        );
    }

    /**
     * @param VisitorRecord $record
     * @param bool $toQueue
     */
    public function syncVisitor(VisitorRecord $record, bool $toQueue = true)
    {
        if ($toQueue === true && $record->inQueue()) {
            HubSpot::warning("Queue Job already exists; ignoring.");
            return;
        }

        $toQueue === true ? $this->syncViaQueue($record) : $this->syncImmediately($record);
    }

    /**
     * @param VisitorRecord $record
     */
    protected function syncViaQueue(VisitorRecord $record)
    {
        Craft::$app->getQueue()->push(
            new SaveVisitor([
                'token' => $record->token,
                'connection' => $record->connection
            ])
        );

        HubSpot::info("Added Queue Job to sync Visitor from HubSpot");
    }

    /**
     * @param VisitorRecord $record
     */
    protected function syncImmediately(VisitorRecord $record)
    {
        try {
            $this->syncFromHubSpot($record);
        } catch (\Exception $e) {
            HubSpot::error(
                sprintf(
                    "Exception caught while trying to sync visitor. Exception: [%s].",
                    (string)Json::encode([
                        'Trace' => $e->getTraceAsString(),
                        'File' => $e->getFile(),
                        'Line' => $e->getLine(),
                        'Code' => $e->getCode(),
                        'Message' => $e->getMessage()
                    ])
                ),
                __METHOD__
            );
        }
    }

    /**
     * @param VisitorRecord $record
     * @throws \Exception
     */
    public function syncFromHubSpot(VisitorRecord $record)
    {
        // Only process 'pending'
        if ($record->status !== VisitorRecord::STATUS_PENDING) {
            return;
        }

        $result = (new ContactCriteria())
            ->setId($record->token)
            ->setConnection($record->connection)
            ->read();

        // Contact doesn't exist.  A known response
        if ($result->getStatusCode() === 404) {
            $record->status = VisitorRecord::STATUS_NOT_FOUND;
            $record->save();
            return;
        }

        // Not sure what happened.
        if ($result->getStatusCode() !== 200) {
            $record->status = VisitorRecord::STATUS_ERROR;
            $record->save();

            throw new Exception(sprintf(
                "Failed to save visitor '%s' due to the following errors: %s:",
                $record->token,
                $result->getBody()->getContents()
            ));
        }

        $event = new PrepVisitorDataFromHubSpotEvent([
            'contact' => Json::decodeIfJson(
                $result->getBody()->getContents()
            )
        ]);

        $record->trigger($event::EVENT_NAME, $event);

        $record->contact = $event->contact;
        $record->status = VisitorRecord::STATUS_SUCCESSFUL;

        $record->save();
    }
}
