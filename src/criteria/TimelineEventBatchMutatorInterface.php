<?php

/**
 * @copyright  Copyright (c) Flipbox Digital Limited
 * @license    https://flipboxfactory.com/software/hubspot/license
 * @link       https://www.flipboxfactory.com/software/hubspot/
 */

namespace flipbox\hubspot\criteria;

use flipbox\hubspot\connections\IntegrationConnectionInterface;
use flipbox\hubspot\transformers\collections\TransformerCollectionInterface;

/**
 * @author Flipbox Factory <hello@flipboxfactory.com>
 * @since 1.0.0
 */
interface TimelineEventBatchMutatorInterface
{
    /**
     * @return array
     */
    public function getPayload(): array;

    /**
     * @return IntegrationConnectionInterface
     */
    public function getConnection(): IntegrationConnectionInterface;

    /**
     * @return TransformerCollectionInterface|null
     */
    public function getTransformer();
}
