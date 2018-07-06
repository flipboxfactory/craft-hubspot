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
class ContactBatchBuilder extends BaseObject implements ContactBatchBuilderInterface
{
    /**
     * @var array|null
     */
    public $payload;

    /**
     * @return array
     */
    public function getPayload(): array
    {
        return (array)$this->payload;
    }
}
