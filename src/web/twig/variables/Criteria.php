<?php

/**
 * @copyright  Copyright (c) Flipbox Digital Limited
 * @license    https://flipboxfactory.com/software/force/license
 * @link       https://www.flipboxfactory.com/software/force/
 */

namespace flipbox\craft\hubspot\web\twig\variables;

use flipbox\craft\hubspot\criteria\CompanyCriteria;
use flipbox\craft\hubspot\criteria\ContactCriteria;
use yii\base\Component;

/**
 * @author Flipbox Factory <hello@flipboxfactory.com>
 * @since 1.0.3
 */
class Criteria extends Component
{
    /**
     * @param array $properties
     * @return CompanyCriteria
     */
    public function getCompany(array $properties = []): CompanyCriteria
    {
        $criteria = (new CompanyCriteria())
            ->populate($properties);

        return $criteria;
    }

    /**
     * @param array $properties
     * @return ContactCriteria
     */
    public function getContact(array $properties = []): ContactCriteria
    {
        $criteria = (new ContactCriteria())
            ->populate($properties);

        return $criteria;
    }
}
