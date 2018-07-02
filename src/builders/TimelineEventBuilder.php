<?php

/**
 * @copyright  Copyright (c) Flipbox Digital Limited
 * @license    https://flipboxfactory.com/software/hubspot/license
 * @link       https://www.flipboxfactory.com/software/hubspot/
 */

namespace flipbox\hubspot\builders;

use craft\helpers\StringHelper;
use yii\base\BaseObject;

/**
 * @author Flipbox Factory <hello@flipboxfactory.com>
 * @since 1.0.0
 */
class TimelineEventBuilder extends BaseObject implements TimelineEventBuilderInterface
{
    /**
     * @var string
     */
    public $id;

    /**
     * @var string|array
     */
    public $object;

    /**
     * The event type Id
     *
     * @var string
     */
    public $typeId;

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
    public function getTypeId(): string
    {
        return $this->typeId;
    }

    /**
     * @return string
     */
    public function getId(): string
    {
        return (string)($this->id ?: StringHelper::randomString());
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