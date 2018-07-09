<?php

/**
 * @copyright  Copyright (c) Flipbox Digital Limited
 * @license    https://flipboxfactory.com/software/hubspot/license
 * @link       https://www.flipboxfactory.com/software/hubspot/
 */

namespace flipbox\hubspot\criteria;

use flipbox\hubspot\services\resources\Companies;

/**
 * @author Flipbox Factory <hello@flipboxfactory.com>
 * @since 1.0.0
 */
class CompanyAccessor extends ObjectAccessor
{
    /**
     * @inheritdoc
     */
    public function init()
    {
        $this->transformer = Companies::defaultTransformer();
        parent::init();
    }
}
