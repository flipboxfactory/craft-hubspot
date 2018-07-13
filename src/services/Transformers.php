<?php

/**
 * @copyright  Copyright (c) Flipbox Digital Limited
 * @license    https://flipboxfactory.com/software/hubspot/license
 * @link       https://www.flipboxfactory.com/software/hubspot/
 */

namespace flipbox\hubspot\services;

use flipbox\flux\Flux;
use yii\base\Component;

/**
 * @author Flipbox Factory <hello@flipboxfactory.com>
 * @since 1.0.0
 */
class Transformers extends Component
{
    /**
     * The scope
     */
    const HUBSPOT_SCOPE = 'hubspot';

    /**
     * @param string $identifier
     * @param string $class
     * @param null $default
     * @return callable|null
     */
    public function find(
        string $identifier,
        string $class,
        $default = null
    ) {
        return Flux::getInstance()->getTransformers()->find(
            $identifier,
            static::HUBSPOT_SCOPE,
            $class,
            $default
        );
    }
}
