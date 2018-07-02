<?php

/**
 * @copyright  Copyright (c) Flipbox Digital Limited
 * @license    https://flipboxfactory.com/software/hubspot/license
 * @link       https://www.flipboxfactory.com/software/hubspot/
 */

namespace flipbox\hubspot\queue\jobs;

use Craft;
use craft\base\ElementInterface;
use craft\queue\BaseJob;
use flipbox\hubspot\fields\Objects;
use flipbox\hubspot\HubSpot;
use yii\base\Component;
use yii\base\InvalidConfigException;

/**
 * @author Flipbox Factory <hello@flipboxfactory.com>
 * @since 1.0.0
 */
abstract class AbstractSyncElementJob extends BaseJob
{
    /**
     * @var Objects
     */
    public $field;

    /**
     * @var ElementInterface
     */
    public $element;

    /**
     * @var string
     */
    public $resource;

    /**
     * @return ElementInterface
     * @throws InvalidConfigException
     */
    protected function getElement(): ElementInterface
    {
        if ($this->isElementInstance($this->element)) {
            return $this->element;
        }

        if (is_numeric($this->element)) {
            $element = Craft::$app->getElements()->getElementById($this->element);
            if ($this->isElementInstance($element)) {
                $this->element = $element;
                return $this->element;
            }
        }

        if (is_string($this->element)) {
            $element = Craft::$app->getElements()->getElementByUri($this->element);
            if ($this->isElementInstance($element)) {
                $this->element = $element;
                return $this->element;
            }
        }

        throw new InvalidConfigException("Unable to resolve element");
    }

    /**
     * @param null $element
     * @return bool
     */
    private function isElementInstance($element = null): bool
    {
        return $element instanceof ElementInterface;
    }

    /**
     * @return Objects
     * @throws InvalidConfigException
     */
    protected function getField(): Objects
    {
        if ($this->isFieldInstance($this->field)) {
            return $this->field;
        }

        if (is_numeric($this->field)) {
            $field = Craft::$app->getFields()->getFieldById($this->field);
            if ($this->isFieldInstance($field)) {
                $this->field = $field;
                return $this->field;
            }
        }

        if (is_string($this->field)) {
            $field = Craft::$app->getFields()->getFieldByHandle($this->field);
            if ($this->isFieldInstance($field)) {
                $this->field = $field;
                return $this->field;
            }
        }

        throw new InvalidConfigException("Unable to resolve field");
    }

    /**
     * @param null $field
     * @return bool
     */
    private function isFieldInstance($field = null): bool
    {
        return $field instanceof Objects;
    }

    /**
     * Todo - Can this be handled more gracefully?
     *
     * @return Component
     * @throws InvalidConfigException
     */
    protected function getResource(): Component
    {
        return HubSpot::getInstance()->getResources()->get($this->resource);
    }
}
