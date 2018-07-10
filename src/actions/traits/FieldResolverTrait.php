<?php

/**
 * @copyright  Copyright (c) Flipbox Digital Limited
 * @license    https://flipboxfactory.com/software/hubspot/license
 * @link       https://www.flipboxfactory.com/software/hubspot/
 */

namespace flipbox\hubspot\actions\traits;

use Craft;
use craft\base\ElementInterface;
use flipbox\hubspot\db\ObjectAssociationQuery;
use flipbox\hubspot\fields\Objects;
use flipbox\hubspot\records\ObjectAssociation;
use yii\web\HttpException;

/**
 * @author Flipbox Factory <hello@flipboxfactory.com>
 * @since 1.0.0
 */
trait FieldResolverTrait
{
    /**
     * @param string $field
     * @return Objects
     * @throws HttpException
     */
    protected function resolveField(string $field): Objects
    {
        $field = is_numeric($field) ?
            Craft::$app->getFields()->getFieldbyId($field) :
            Craft::$app->getFields()->getFieldByHandle($field);

        if (!$field instanceof Objects) {
            throw new HttpException(400, sprintf(
                "Field must be an instance of '%s', '%s' given.",
                Objects::class,
                get_class($field)
            ));
        }

        return $field;
    }

    /**
     * @param Objects $field
     * @param ElementInterface $element
     * @param string $objectId
     * @return ObjectAssociation
     * @throws HttpException
     */
    protected function resolveRecord(Objects $field, ElementInterface $element, string $objectId): ObjectAssociation
    {
        /** @var ObjectAssociationQuery $query */
        if (null === ($query = $element->getFieldValue($field->handle))) {
            throw new HttpException(400, 'Field is not associated to element');
        }

        if (null === ($criteria = $query->objectId($objectId)->one())) {
            throw new HttpException(400, 'Invalid value');
        };

        return $criteria;
    }
}
