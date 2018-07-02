<?php

/**
 * @copyright  Copyright (c) Flipbox Digital Limited
 * @license    https://flipboxfactory.com/software/hubspot/license
 * @link       https://www.flipboxfactory.com/software/hubspot/
 */

namespace flipbox\hubspot\queue\jobs;

use Craft;
use craft\queue\BaseJob;
use flipbox\hubspot\connections\IntegrationConnectionInterface;
use flipbox\hubspot\HubSpot;

/**
 * @author Flipbox Factory <hello@flipboxfactory.com>
 * @since 1.0.0
 */
class UpsertTimelineEvent extends BaseJob
{
    /**
     * @var string
     */
    public $id;

    /**
     * @var string
     */
    public $typeId;

    /**
     * @var array
     */
    public $payload = [];

    /**
     * @var IntegrationConnectionInterface
     */
    public $connection;

    /**
     * @inheritdoc
     * @throws \yii\base\InvalidConfigException
     */
    public function execute($queue)
    {
        HubSpot::getInstance()->getResources()->getTimelineEvents()->rawUpsert(
            $this->typeId,
            $this->id,
            $this->payload,
            $this->connection
        );
    }

    /**
     * @inheritdoc
     */
    protected function defaultDescription(): string
    {
        return Craft::t('hubspot', 'Create/Update HubSpot timeline event.');
    }
}
