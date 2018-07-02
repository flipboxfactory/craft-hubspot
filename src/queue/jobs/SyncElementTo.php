<?php

/**
 * @copyright  Copyright (c) Flipbox Digital Limited
 * @license    https://flipboxfactory.com/software/hubspot/license
 * @link       https://www.flipboxfactory.com/software/hubspot/
 */

namespace flipbox\hubspot\queue\jobs;

use Craft;

/**
 * @author Flipbox Factory <hello@flipboxfactory.com>
 * @since 1.0.0
 */
class SyncElementTo extends AbstractSyncElementJob
{
    /**
     * @param \craft\queue\QueueInterface|\yii\queue\Queue $queue
     * @return bool|false|string
     * @throws \yii\base\InvalidConfigException
     */
    public function execute($queue)
    {
        return $this->getResource()->syncUp(
            $this->getElement(),
            $this->getField()
        );
    }

    /**
     * @inheritdoc
     */
    protected function defaultDescription(): string
    {
        return Craft::t('hubspot', 'Syncing a Craft Element to a HubSpot ' . $this->resource);
    }
}
