<?php

/**
 * @copyright  Copyright (c) Flipbox Digital Limited
 * @license    https://flipboxfactory.com/software/hubspot/license
 * @link       https://www.flipboxfactory.com/software/hubspot/
 */

namespace flipbox\craft\hubspot\events;

use craft\base\Element;
use craft\base\ElementInterface;
use craft\helpers\StringHelper;
use flipbox\craft\hubspot\fields\ObjectsFieldInterface;
use Psr\Http\Message\ResponseInterface;
use yii\base\Event;

/**
 * @property ElementInterface|Element $sender
 */
class PopulateElementFromResponseEvent extends Event
{
    /**
     * @var ResponseInterface
     */
    private $response;

    /**
     * @var ObjectsFieldInterface
     */
    private $field;

    /**
     * @param ResponseInterface $response
     * @return $this
     */
    public function setResponse(ResponseInterface $response)
    {
        $this->response = $response;
        return $this;
    }

    /**
     * @return ResponseInterface
     */
    public function getResponse(): ResponseInterface
    {
        return $this->response;
    }

    /**
     * @param ObjectsFieldInterface $field
     * @return $this
     */
    public function setField(ObjectsFieldInterface $field)
    {
        $this->field = $field;
        return $this;
    }

    /**
     * @return ObjectsFieldInterface
     */
    public function getField(): ObjectsFieldInterface
    {
        return $this->field;
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
            'populate',
            $object,
            $action
        ]);

        return StringHelper::toLowerCase(
            StringHelper::toString($name, ':')
        );
    }
}
