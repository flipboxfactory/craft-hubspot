<?php

/**
 * @copyright  Copyright (c) Flipbox Digital Limited
 * @license    https://github.com/flipbox/hubspot/blob/master/LICENSE.md
 * @link       https://github.com/flipbox/hubspot
 */

namespace flipbox\craft\hubspot\transformers;

/**
 * @author Flipbox Factory <hello@flipboxfactory.com>
 * @since 1.0.0
 */
class InterpretUpsertResponseErrors extends InterpretResponseErrors
{
    use UpsertErrorInterpreterTrait;
}
