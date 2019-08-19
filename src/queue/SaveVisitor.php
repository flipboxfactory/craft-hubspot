<?php

/**
 * @copyright  Copyright (c) Flipbox Digital Limited
 * @license    https://flipboxfactory.com/software/hubspot/license
 * @link       https://www.flipboxfactory.com/software/hubspot/
 */

namespace flipbox\craft\hubspot\queue;

use Craft;
use craft\queue\BaseJob;
use flipbox\craft\hubspot\criteria\ContactCriteria;
use flipbox\craft\hubspot\records\Visitor;
use yii\base\Exception;

/**
 * Sync a HubSpot Object to a Craft Element
 */
class SaveVisitor extends BaseJob implements \Serializable
{
    /**
     * @var string|null
     */
    public $token;

    /**
     * The HubSpot connection identifier
     *
     * @var string|null
     */
    public $connection;

    /**
     * Returns a default description for [[getDescription()]].
     *
     * @return string|null
     */
    protected function defaultDescription()
    {
        return "Saving HubSpot Visitor by Token: " . $this->token;
    }

    /**
     * @inheritDoc
     * @throws \Exception
     */
    public function execute($queue)
    {
        if (empty($this->token)) {
            return;
        }

        $record = Visitor::findOrCreate($this->token);

        // Only process 'pending'
        if ($record->status !== Visitor::STATUS_PENDING) {
            return;
        }

        $result = (new ContactCriteria())
            ->setId($this->token)
            ->setConnection($this->connection)
            ->read();

        // Contact doesn't exist.  A known response
        if ($result->getStatusCode() === 404) {
            $record->status = Visitor::STATUS_NOT_FOUND;
            $record->save();
            return;
        }

        // Not sure what happened.
        if ($result->getStatusCode() !== 200) {
            $record->status = Visitor::STATUS_ERROR;
            $record->save();

            throw new Exception(sprintf(
                "Failed to save visitor '%s' due to the following errors: %s:",
                $this->token,
                $result->getBody()->getContents()
            ));
        }

        $record = Visitor::findOrCreate($this->token);
        $record->contact = $result->getBody()->getContents();
        $record->status = Visitor::STATUS_SUCCESSFUL;
        $record->save();
    }

    /**
     * @inheritdoc
     */
    public function serialize()
    {
        return serialize([
            'token' => $this->token,
            'connection' => $this->connection
        ]);
    }

    /**
     * @inheritdoc
     */
    public function unserialize($serialized)
    {
        Craft::configure(
            $this,
            unserialize($serialized)
        );
    }
}
