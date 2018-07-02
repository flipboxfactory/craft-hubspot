<?php

/**
 * @copyright  Copyright (c) Flipbox Digital Limited
 * @license    https://flipboxfactory.com/software/hubspot/license
 * @link       https://www.flipboxfactory.com/software/hubspot/
 */

namespace flipbox\hubspot\transformers\collections;

use flipbox\hubspot\helpers\TransformerHelper;

/**
 * @author Flipbox Factory <hello@flipboxfactory.com>
 * @since 1.0.0
 */
trait TransformerCollectionTrait
{
    /**
     * @var array
     */
    protected $transformers = [];

    /*******************************************
     * TRANSFORMER
     *******************************************/

    /**
     * @param $transformers
     * @return $this
     */
    public function setTransformers($transformers)
    {
        if (!is_array($transformers)) {
            $transformers = empty($transformers) ? [] : ['default' => $transformers];
        }

        foreach ($transformers as $key => $value) {
            $this->addTransformer($key, $value);
        }

        return $this;
    }

    /**
     * @param string $key
     * @param $transformer
     * @return $this
     */
    public function addTransformer(string $key, $transformer)
    {
        $this->transformers[$key] = $transformer;
        return $this;
    }

    /**
     * @param string $key
     * @return callable|\Flipbox\Transform\Transformers\TransformerInterface|null
     * @throws \Flipbox\Skeleton\Exceptions\InvalidConfigurationException
     */
    public function getTransformer(string $key)
    {
        if (!array_key_exists($key, $this->transformers)) {
            return null;
        }

        return TransformerHelper::resolve($this->transformers[$key]);
    }
}
