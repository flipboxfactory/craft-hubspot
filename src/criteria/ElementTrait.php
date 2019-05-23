<?php

/**
 * @copyright  Copyright (c) Flipbox Digital Limited
 * @license    https://flipboxfactory.com/software/hubspot/license
 * @link       https://www.flipboxfactory.com/software/hubspot/
 */

namespace flipbox\craft\hubspot\criteria;

use flipbox\craft\ember\objects\ElementAttributeTrait;
use flipbox\craft\ember\objects\FieldAttributeTrait;
use flipbox\craft\ember\objects\SiteAttributeTrait;
use flipbox\craft\hubspot\fields\ObjectsFieldInterface;

/**
 * @author Flipbox Factory <hello@flipboxfactory.com>
 * @since 1.0.0
 *
 * @method ObjectsFieldInterface getField()
 */
trait ElementTrait
{
    use IdAttributeFromElementTrait,
        PayloadAttributeFromElementTrait,
        ElementAttributeTrait,
        FieldAttributeTrait,
        SiteAttributeTrait;
}
