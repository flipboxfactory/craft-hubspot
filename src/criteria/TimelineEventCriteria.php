<?php

/**
 * @copyright  Copyright (c) Flipbox Digital Limited
 * @license    https://flipboxfactory.com/software/hubspot/license
 * @link       https://www.flipboxfactory.com/software/hubspot/
 */

namespace flipbox\craft\hubspot\criteria;

/**
 * @author Flipbox Factory <hello@flipboxfactory.com>
 * @since 1.0.0
 */
class TimelineEventCriteria extends \Flipbox\HubSpot\Criteria\TimelineEventCriteria
{
    use CacheTrait,
        IntegrationConnectionTrait;

    /**
     * @var string|array
     */
    public $object;

    /**
     * @var array
     */
    public $extraData = [];

    /**
     * @var array|null
     */
    public $payload;

    /**
     * @var array
     */
    public $properties = [];

    /**
     * @var array
     */
    public $timelineIFrame = [];

    /**
     * @return string
     */
    public function getId(): string
    {
        $id = $this->findId();
        return (string)($id ?: substr(str_shuffle(md5(time())), 0, 36));
    }

    /**
     * @return array
     */
    public function getPayload(): array
    {
        // Explicitly set ?
        if ($this->payload !== null) {
            return (array)$this->payload;
        }

        $payload = array_merge(
            $this->getObjectPayload(),
            [
                'timelineIFrame' => $this->timelineIFrame,
                'extraData' => $this->extraData
            ],
            $this->properties
        );

        return array_filter($payload);
    }

    /**
     * @return array
     */
    protected function getObjectPayload(): array
    {
        if (is_numeric($this->object)) {
            $this->object = ['objectId' => $this->object];
        }

        return (array)$this->object;
    }
}
