<?php

/**
 * @copyright  Copyright (c) Flipbox Digital Limited
 * @license    https://flipboxfactory.com/software/hubspot/license
 * @link       https://www.flipboxfactory.com/software/hubspot/
 */

namespace flipbox\craft\hubspot\criteria;

use craft\helpers\ArrayHelper;
use Flipbox\HubSpot\Criteria\AbstractCriteria;
use Flipbox\HubSpot\Criteria\PayloadAttributeTrait;
use Flipbox\HubSpot\Resources\TimelineEvent;
use Psr\Http\Message\ResponseInterface;

/**
 * @author Flipbox Factory <hello@flipboxfactory.com>
 * @since 1.0.0
 */
class TimelineEventBatchCriteria extends AbstractCriteria
{
    use IntegrationConnectionTrait,
        PayloadAttributeTrait;

    /**
     * @return array
     */
    public function getPayload(): array
    {
        $payload = array_filter((array)$this->payload);

        return [
            'eventWrappers' => ArrayHelper::remove($payload, 'eventWrappers', $payload)
        ];
    }

    /**
     * @param array $items
     * @return $this
     * @throws \Exception
     */
    public function setItems(array $items)
    {
        foreach ($items as $item) {
            if (!$item instanceof TimelineEventCriteria) {
                continue;
            }

            $this->addItem($item);
        }

        return $this;
    }

    /**
     * @param TimelineEventCriteria $criteria
     * @return $this
     * @throws \Exception
     */
    public function addItem(TimelineEventCriteria $criteria)
    {
        $this->payload[] = $criteria->getPayload();
        return $this;
    }

    /**
     * @param array $criteria
     * @param array $config
     * @return ResponseInterface
     * @throws \Exception
     */
    public function batch(array $criteria = [], array $config = []): ResponseInterface
    {
        $this->populate($criteria);

        return TimelineEvent::batch(
            $this->getPayload(),
            $this->getConnection(),
            $this->getLogger(),
            $config
        );
    }
}
