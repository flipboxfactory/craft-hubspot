<?php

/**
 * @copyright  Copyright (c) Flipbox Digital Limited
 * @license    https://flipboxfactory.com/software/hubspot/license
 * @link       https://www.flipboxfactory.com/software/hubspot/
 */

namespace flipbox\craft\hubspot\queue;

use Craft;
use craft\queue\BaseJob;
use flipbox\craft\hubspot\HubSpot;
use flipbox\craft\hubspot\records\Visitor as VisitorRecord;

/**
 * Sync a HubSpot Object to a Craft Element
 */
class SaveVisitor extends BaseJob implements \Serializable
{
    /**
     * The default description
     */
    const DESCRIPTION = "Saving HubSpot Visitor by Token: ";

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
    protected function defaultDescription(): ?string
    {
        return static::DESCRIPTION . $this->token;
    }

    /**
     * @inheritDoc
     * @throws \Exception
     */
    public function execute($queue): void
    {
        if (empty($this->token)) {
            return;
        }

        // Sync
        HubSpot::getInstance()->getVisitor()->syncFromHubSpot(
            VisitorRecord::findOrCreate(
                $this->token,
                $this->connection
            )
        );
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
