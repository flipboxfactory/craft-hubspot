<?php

namespace flipbox\hubspot\fields;

use Craft;
use craft\base\Element;
use craft\base\ElementInterface;
use craft\base\Field;
use craft\base\PreviewableFieldInterface;

abstract class AbstractField extends Field implements PreviewableFieldInterface
{

    const FIELD_TYPE = '';

    /**
     * @inheritdoc
     */
    public static function displayName(): string
    {
        return Craft::t('hubspot', 'Hubspot: ' . static::FIELD_TYPE);
    }

    /**
     * @param ElementInterface $element
     * @return null|string
     */
    protected abstract function findObjectId(ElementInterface $element);


    /**
     * @inheritdoc
     */
    public static function hasContentColumn(): bool
    {
        return false;
    }

    /**
     * @inheritdoc
     */
    public static function supportedTranslationMethods(): array
    {
        return [
            self::TRANSLATION_METHOD_SITE,
        ];
    }

    /**
     * @inheritdoc
     */
    public function normalizeValue($value, ElementInterface $element = null)
    {

        if (is_string($value) || is_numeric($value)) {
            return $value;
        }

        if (is_null($value) && $element !== null && !empty($element->getId())) {
            return $this->findObjectId($element);
        }

        return $value;
    }


    /**
     * @inheritdoc
     */
    public function getTableAttributeHtml($value, ElementInterface $element): string
    {
        return is_string($value) ? $value : '';
    }

    /**
     * @inheritdoc
     */
    public function getInputHtml($value, ElementInterface $element = null): string
    {
        /** @var Element $element */
        return Craft::$app->getView()->renderTemplate(
            'hubspot/_components/fieldtypes/' . static::FIELD_TYPE . '/input',
            [
                'name' => $this->handle,
                'value' => $value,
                'field' => $this,
                'hasErrors' => $element->hasErrors($this->handle)
            ]
        );
    }
}