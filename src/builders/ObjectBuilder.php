<?php

/**
 * @copyright  Copyright (c) Flipbox Digital Limited
 * @license    https://flipboxfactory.com/software/hubspot/license
 * @link       https://www.flipboxfactory.com/software/hubspot/
 */

namespace flipbox\hubspot\builders;

use yii\base\BaseObject;

/**
 * @author Flipbox Factory <hello@flipboxfactory.com>
 * @since 1.0.0
 */
class ObjectBuilder extends BaseObject implements ObjectBuilderInterface
{
    /**
     * @var string
     */
    public $id;

    /**
     * @var array|null
     */
    public $payload;

    /**
     * @return string
     */
    public function getId()
    {
        return empty($this->id) ? null : (string)$this->id;
    }

    /**
     * @return array
     */
    public function getPayload(): array
    {
        return array_filter((array)$this->payload);
    }
}
