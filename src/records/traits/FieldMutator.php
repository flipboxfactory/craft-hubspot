<?php

/**
 * @copyright  Copyright (c) Flipbox Digital Limited
 * @license    https://flipboxfactory.com/software/hubspot/license
 * @link       https://www.flipboxfactory.com/software/hubspot/
 */

namespace flipbox\hubspot\records\traits;

use Craft;
use craft\base\Field;
use craft\base\FieldInterface;

/**
 * @property int|null $fieldId
 * @property Field|FieldInterface|null $field
 *
 * @author Flipbox Factory <hello@flipboxfactory.com>
 * @since 1.0.0
 */
trait FieldMutator
{
    /**
     * @var Field|null
     */
    private $field;

    /**
     * Set associated fieldId
     *
     * @param $id
     * @return $this
     */
    public function setFieldId(int $id)
    {
        $this->fieldId = $id;
        return $this;
    }

    /**
     * Get associated fieldId
     *
     * @return int|null
     */
    public function getFieldId()
    {
        if (null === $this->fieldId && null !== $this->field) {
            $this->fieldId = $this->field->id;
        }

        return $this->fieldId;
    }

    /**
     * Associate a field
     *
     * @param mixed $field
     * @return $this
     */
    public function setField($field = null)
    {
        $this->field = null;

        if (!$field = $this->internalResolveField($field)) {
            $this->field = $this->fieldId = null;
        } else {
            /** @var Field $field */
            $this->fieldId = $field->id;
            $this->field = $field;
        }

        return $this;
    }

    /**
     * @return FieldInterface|null
     */
    public function getField()
    {
        /** @var Field $field */
        if ($this->field === null) {
            $field = $this->resolveField();
            $this->setField($field);
            return $field;
        }

        $fieldId = $this->fieldId;
        if ($fieldId !== null &&
            $fieldId !== $this->field->id
        ) {
            $this->field = null;
            return $this->getField();
        }

        return $this->field;
    }

    /**
     * @return FieldInterface|null
     */
    protected function resolveField()
    {
        if ($model = $this->resolveFieldFromId()) {
            return $model;
        }

        return null;
    }

    /**
     * @return FieldInterface|null
     */
    private function resolveFieldFromId()
    {
        if (null === $this->fieldId) {
            return null;
        }

        return Craft::$app->getFields()->getFieldById($this->fieldId);
    }

    /**
     * @param mixed $field
     * @return FieldInterface|Field|null
     */
    protected function internalResolveField($field = null)
    {
        if ($field instanceof FieldInterface) {
            return $field;
        }

        if (is_numeric($field)) {
            return Craft::$app->getFields()->getFieldById($field);
        }

        if (is_string($field)) {
            return Craft::$app->getFields()->getFieldByHandle($field);
        }

        return Craft::$app->getFields()->createField($field);
    }
}
