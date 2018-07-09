<?php

/**
 * @copyright  Copyright (c) Flipbox Digital Limited
 * @license    https://flipboxfactory.com/software/hubspot/license
 * @link       https://www.flipboxfactory.com/software/hubspot/
 */

namespace flipbox\hubspot\transformers\collections;

use flipbox\flux\helpers\TransformerHelper;
use flipbox\hubspot\HubSpot;

/**
 * @author Flipbox Factory <hello@flipboxfactory.com>
 * @since 1.0.0
 */
class DynamicTransformerCollection extends TransformerCollection
{
    /**
     * The transformer handle parts.  We'll assemble these in to a string such as 'sobject:account:response'
     *
     * @var array
     */
    protected $handle = [];

    /**
     * The resource class name.  Transformers can be registered to an object.  In this case, we'll
     * retrieve transformers that have been registered against the resources identified below.  Multiple
     * 'resource' classes can be provided; we'll iterate through each and return the first match.
     *
     * @var array
     */
    protected $resource = [];

    /**
     * @param array|string $resources
     * @return $this
     */
    public function setResource($resources = [])
    {
        if (!is_array($resources)) {
            $resources = [$resources];
        }

        foreach (array_filter($resources) as $resource) {
            $this->addResource($resource);
        }
        return $this;
    }

    /**
     * @param $resource
     * @return $this
     */
    public function addResource($resource)
    {
        if (!in_array($resource, $this->resource)) {
            $this->resource[] = $resource;
        }

        return $this;
    }

    /**
     * @param array|string $handles
     * @return $this
     */
    public function setHandle($handles = [])
    {
        if (!is_array($handles)) {
            $handles = [$handles];
        }

        foreach (array_filter($handles) as $handle) {
            $this->addHandle($handle);
        }
        return $this;
    }

    /**
     * @param $handle
     * @return $this
     */
    public function addHandle($handle)
    {
        if (!in_array($handle, $this->handle)) {
            $this->handle[] = $handle;
        }

        return $this;
    }

    /**
     * @inheritdoc
     */
    public function getTransformer(string $key)
    {
        if (null === ($transformer = $this->dynamicTransformer($key))) {
            $transformer = parent::getTransformer($key);
        }

        return $transformer;
    }

    /**
     * @param string $key
     * @return callable|\Flipbox\Transform\Transformers\TransformerInterface|null
     */
    protected function dynamicTransformer(string $key)
    {
        $event = TransformerHelper::eventName(array_merge($this->handle, [$key]));

        if (null === ($transformer = $this->resolveTransformer($event))) {
            HubSpot::warning(sprintf(
                "Unable to resolve transformer via event '%s'.",
                $event
            ), __METHOD__);
        }

        return $transformer;
    }

    /**s
     * @param string $eventName
     * @return callable|\Flipbox\Transform\Transformers\TransformerInterface|null
     */
    protected function resolveTransformer(string $eventName)
    {
        foreach ($this->resource as $class) {
            $transformer = HubSpot::getInstance()->getTransformers()->find(
                $eventName,
                $class
            );

            if ($transformer !== null) {
                return $transformer;
            }
        }

        return null;
    }
}
