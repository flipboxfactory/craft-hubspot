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
    public function findTokenValue()
    {
        return $_COOKIE[self::COOKIE_NAME] ?? null;
    }

    /**
     * @param string|null $connection
     * @return VisitorRecord|null
     */
    public function findRecord(string $connection = null)
    {
        if (null === ($token = $this->findTokenValue())) {
            HubSpot::info("Visitor token was not found.");
            return null;
        }

        return VisitorRecord::findOrCreate($token, $connection);
    }

    /**
     * @param bool $toQueue
     * @param string|null $connection
     * @param string|null $cache
     * @return mixed|null
     */
    public function findContact(bool $toQueue = true, string $connection = null, string $cache = null)
    {
        if (null === ($record = $this->findRecord($connection))) {
            return null;
        }

        if ($record->status !== VisitorRecord::STATUS_SUCCESSFUL) {
            // If new, save now so we can reference later
            if ($record->getIsNewRecord()) {
                $record->save();
            }

            if ($record->status === VisitorRecord::STATUS_PENDING) {
                $this->syncVisitor($record, $toQueue, $cache);
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
     * @param string|null $cache
     */
    public function syncVisitor(VisitorRecord $record, bool $toQueue = true, string $cache = null)
    {
        if ($toQueue === true && $record->inQueue()) {
            HubSpot::warning("Queue Job already exists; ignoring.");
            return;
        }

        $toQueue === true ? $this->syncViaQueue($record, $cache) : $this->syncImmediately($record, $cache);
    }

    /**
     * @param VisitorRecord $record
     * @param string|null $cache
     */
    protected function syncViaQueue(VisitorRecord $record, string $cache = null)
    {
        Craft::$app->getQueue()->push(
            new SaveVisitor([
                'token' => $record->token,
                'connection' => $record->connection,
                'cache' => $cache
            ])
        );

        HubSpot::info("Added Queue Job to sync Visitor from HubSpot");
    }

    /**
     * @param VisitorRecord $record
     * @param string|null $cache
     */
    protected function syncImmediately(VisitorRecord $record, string $cache = null)
    {
        try {
            $this->syncFromHubSpot($record, $cache);
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
     * @param string|null $cache
     * @throws \Exception
     */
    public function syncFromHubSpot(VisitorRecord $record, string $cache = null)
    {
        // Only process 'pending'
        if ($record->status !== VisitorRecord::STATUS_PENDING) {
            return;
        }

        $result = (new ContactCriteria())
            ->setId($record->token)
            ->setConnection($record->connection)
            ->setCache($cache)
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
