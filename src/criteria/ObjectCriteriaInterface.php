<?php

/**
 * @copyright  Copyright (c) Flipbox Digital Limited
 * @license    https://flipboxfactory.com/software/hubspot/license
 * @link       https://www.flipboxfactory.com/software/hubspot/
 */

namespace flipbox\hubspot\criteria;

use flipbox\hubspot\connections\ConnectionInterface;
use flipbox\hubspot\transformers\collections\TransformerCollectionInterface;
use Psr\SimpleCache\CacheInterface;

/**
 * @author Flipbox Factory <hello@flipboxfactory.com>
 * @since 1.0.0
 */
interface ObjectCriteriaInterface
{
    /**
     * @return string|null
     */
    public function getId();

    /**
     * @param string $id
     * @return static
     */
    public function setId(string $id = null);

    /**
     * @return ConnectionInterface
     */
    public function getConnection(): ConnectionInterface;

    /**
     * @return CacheInterface
     */
    public function getCache(): CacheInterface;

    /**
     * @return TransformerCollectionInterface|null
     */
    public function getTransformer();

    /**
     * @param array $config
     * @return mixed
     */
    public function fetch(array $config = []);
}
