<?php

/**
 * @copyright  Copyright (c) Flipbox Digital Limited
 * @license    https://flipboxfactory.com/software/hubspot/license
 * @link       https://www.flipboxfactory.com/software/hubspot/
 */

namespace flipbox\hubspot\criteria\element;

use Craft;
use craft\base\ElementInterface;
use craft\errors\ElementNotFoundException;
use craft\errors\FieldNotFoundException;
use flipbox\hubspot\criteria\ObjectCriteriaInterface;
use flipbox\hubspot\criteria\traits\CacheTrait;
use flipbox\hubspot\criteria\traits\ConnectionTrait;
use flipbox\hubspot\criteria\traits\TransformerCollectionTrait;
use flipbox\hubspot\fields\Objects;
use flipbox\hubspot\traits\TransformElementIdTrait;
use yii\base\BaseObject;

/**
 * @author Flipbox Factory <hello@flipboxfactory.com>
 * @since 1.0.0
 */
class ObjectFromElementCriteria extends BaseObject implements ObjectCriteriaInterface
{
    use TransformElementIdTrait,
        TransformerCollectionTrait,
        ConnectionTrait,
        CacheTrait;

    /**
     * @var ElementInterface
     */
    public $element;

    /**
     * @var Objects
     */
    public $field;

    /**
     * @return ElementInterface
     * @throws ElementNotFoundException
     */
    protected function getElement(): ElementInterface
    {
        if ($this->element instanceof ElementInterface) {
            return $this->element;
        }

        if (is_numeric($this->element)) {
            return $this->element = Craft::$app->getElements()->getElementById($this->element);
        }

        if (is_string($this->element)) {
            return $this->element = Craft::$app->getElements()->getElementByUri($this->element);
        }

        throw new ElementNotFoundException("Unable to resolve element.");
    }

    /**
     * @return Objects
     * @throws FieldNotFoundException
     */
    protected function getField(): Objects
    {
        if ($this->field instanceof Objects) {
            return $this->field;
        }

        $field = null;
        if (is_numeric($this->field)) {
            $field = Craft::$app->getFields()->getFieldById($this->field);
        } elseif (is_string($this->element)) {
            $field = Craft::$app->getFields()->getFieldByHandle($this->field);
        }

        if ($field instanceof Objects) {
            return $this->field = $field;
        }

        throw new FieldNotFoundException("Unable to resolve field.");
    }

    /**
     * @inheritdoc
     * @throws FieldNotFoundException
     * @throws ElementNotFoundException
     */
    public function getId(): string
    {
        return $this->transformElementId(
            $this->getElement(),
            $this->getField()
        );
    }
}
