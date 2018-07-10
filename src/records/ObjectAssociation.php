<?php

/**
 * @copyright  Copyright (c) Flipbox Digital Limited
 * @license    https://flipboxfactory.com/software/hubspot/license
 * @link       https://www.flipboxfactory.com/software/hubspot/
 */

namespace flipbox\hubspot\records;

use Craft;
use flipbox\craft\sortable\associations\records\SortableAssociation;
use flipbox\craft\sortable\associations\services\SortableAssociations;
use flipbox\ember\helpers\ModelHelper;
use flipbox\ember\records\traits\ElementAttribute;
use flipbox\ember\records\traits\SiteAttribute;
use flipbox\hubspot\db\ObjectAssociationQuery;
use flipbox\hubspot\fields\Objects;
use flipbox\hubspot\HubSpot;
use flipbox\hubspot\services\ObjectAssociations;

/**
 * @author Flipbox Factory <hello@flipboxfactory.com>
 * @since 1.0.0
 *
 * @property int $fieldId
 * @property string $objectId
 */
class ObjectAssociation extends SortableAssociation
{
    use SiteAttribute,
        ElementAttribute,
        traits\FieldAttribute;

    /**
     * The table alias
     */
    const TABLE_ALIAS = 'hubspot_objects';

    /**
     * @inheritdoc
     */
    const TARGET_ATTRIBUTE = 'objectId';

    /**
     * @inheritdoc
     */
    const SOURCE_ATTRIBUTE = 'elementId';

    /**
     * The default HubSpot Resource Id (if none exists)
     */
    const DEFAULT_HUBSPOT_ID = 'UNKNOWN_ID';

    /**
     * @inheritdoc
     */
    protected $getterPriorityAttributes = ['fieldId', 'elementId', 'siteId'];

    /**
     * @inheritdoc
     * @throws \Throwable
     */
    public function __construct($config = [])
    {
        HubSpot::getInstance()->getObjectAssociations()->ensureTableExists();
        parent::__construct($config);
    }

    /**
     * @noinspection PhpDocMissingThrowsInspection
     * @return ObjectAssociationQuery
     */
    public static function find(): ObjectAssociationQuery
    {
        /** @noinspection PhpIncompatibleReturnTypeInspection */
        /** @noinspection PhpUnhandledExceptionInspection */
        return Craft::createObject(ObjectAssociationQuery::class, [get_called_class()]);
    }

    /**
     * {@inheritdoc}
     */
    public static function tableAlias()
    {
        return parent::tableAlias() . HubSpot::getInstance()->getSettings()->environmentTablePostfix;
    }

    /**
     * @inheritdoc
     * @return ObjectAssociations
     */
    protected function associationService(): SortableAssociations
    {
        return HubSpot::getInstance()->getObjectAssociations();
    }

    /**
     * @param array $criteria
     * @return mixed|null
     * @throws \yii\base\InvalidConfigException
     */
    public function getResource(array $criteria = [])
    {
        if (null === ($field = $this->getField())) {
            return null;
        }

        if (!$field instanceof Objects) {
            return null;
        }

        $resource = $field->getResource();

        $criteria['id'] = $this->objectId ?: self::DEFAULT_HUBSPOT_ID;

        return $resource->read(
            $resource->getAccessorCriteria($criteria)
        );
    }

    /**
     * @return array
     */
    public function rules(): array
    {
        return array_merge(
            parent::rules(),
            $this->siteRules(),
            $this->elementRules(),
            $this->fieldRules(),
            [
                [
                    [
                        self::TARGET_ATTRIBUTE,
                    ],
                    'required'
                ],
                [
                    self::TARGET_ATTRIBUTE,
                    'unique',
                    'targetAttribute' => [
                        'elementId',
                        'fieldId',
                        'siteId',
                        self::TARGET_ATTRIBUTE
                    ]
                ],
                [
                    [
                        self::TARGET_ATTRIBUTE
                    ],
                    'safe',
                    'on' => [
                        ModelHelper::SCENARIO_DEFAULT
                    ]
                ]
            ]
        );
    }
}
