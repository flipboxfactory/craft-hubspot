<?php

/**
 * @copyright  Copyright (c) Flipbox Digital Limited
 * @license    https://flipboxfactory.com/software/hubspot/license
 * @link       https://www.flipboxfactory.com/software/hubspot/
 */

namespace flipbox\craft\hubspot\events;

use craft\base\ElementInterface;
use craft\helpers\StringHelper;
use yii\base\Event;

/**
 * @param ElementInterface $sender
 */
class CreatePayloadFromElementEvent extends Event
{
    /**
     * @var array
     */
    private $payload = [];

    /**
     * @param array $payload
     * @return $this
     */
    public function setPayload(array $payload)
    {
        $this->payload = $payload;
        return $this;
    }

    /**
     * @return array
     */
    public function getPayload(): array
    {
        return $this->payload;
    }

    /**
     * @param string $object
     * @param string|null $action
     * @return string
     */
    public static function eventName(
        string $object,
        string $action = null
    ): string {
        $name = array_filter([
            'payload',
            $object,
            $action
        ]);

        return StringHelper::toLowerCase(
            StringHelper::toString($name, ':')
        );
    }
}
