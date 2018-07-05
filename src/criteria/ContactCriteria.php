<?php

/**
 * @copyright  Copyright (c) Flipbox Digital Limited
 * @license    https://flipboxfactory.com/software/hubspot/license
 * @link       https://www.flipboxfactory.com/software/hubspot/
 */

namespace flipbox\hubspot\criteria;

use flipbox\hubspot\services\resources\Contacts;

/**
 * @author Flipbox Factory <hello@flipboxfactory.com>
 * @since 1.0.0
 */
class ContactCriteria extends ObjectCriteria
{
    /**
     * @inheritdoc
     */
    public function init()
    {
        $this->transformer = Contacts::defaultTransformer();
        parent::init();
    }
}
