<?php

/**
 * @copyright  Copyright (c) Flipbox Digital Limited
 * @license    https://flipboxfactory.com/software/hubspot/license
 * @link       https://www.flipboxfactory.com/software/hubspot/
 */

namespace flipbox\hubspot\criteria;

use flipbox\hubspot\connections\IntegrationConnectionInterface;
use flipbox\hubspot\transformers\collections\TransformerCollectionInterface;
use Psr\SimpleCache\CacheInterface;

/**
 * @author Flipbox Factory <hello@flipboxfactory.com>
 * @since 1.0.0
 */
interface TimelineEventAccessorInterface
{
    /**
     * @return string
     */
    public function getId();

    /**
     * @return string
     */
    public function getTypeId();

    /**
     * @return IntegrationConnectionInterface
     */
    public function getConnection(): IntegrationConnectionInterface;

    /**
     * @return CacheInterface
     */
    public function getCache(): CacheInterface;

    /**
     * @return TransformerCollectionInterface|null
     */
    public function getTransformer();
}
