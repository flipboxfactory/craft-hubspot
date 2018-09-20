<?php

/**
 * @copyright  Copyright (c) Flipbox Digital Limited
 * @license    https://flipboxfactory.com/software/hubspot/license
 * @link       https://www.flipboxfactory.com/software/hubspot/
 */

namespace flipbox\hubspot\criteria;

use yii\base\BaseObject;

/**
 * @author Flipbox Factory <hello@flipboxfactory.com>
 * @since 1.0.0
 */
class TimelineEventBatchMutator extends BaseObject implements TimelineEventBatchMutatorInterface
{
    use traits\TransformerCollectionTrait,
        traits\IntegrationConnectionTrait;

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
