<?php

/**
 * @copyright  Copyright (c) Flipbox Digital Limited
 * @license    https://flipboxfactory.com/software/hubspot/license
 * @link       https://www.flipboxfactory.com/software/hubspot/
 */

namespace flipbox\hubspot\fields;

use Craft;
use craft\base\ElementInterface;
use craft\base\Field;
use craft\elements\db\ElementQueryInterface;
use flipbox\ember\helpers\ModelHelper;
use flipbox\ember\validators\MinMaxValidator;
use flipbox\hubspot\criteria\CompanyCriteria;
use flipbox\hubspot\criteria\ContactCriteria;
use flipbox\hubspot\criteria\ContactListCriteria;
use flipbox\hubspot\criteria\ObjectCriteria;
use flipbox\hubspot\criteria\ObjectCriteriaInterface;
use flipbox\hubspot\db\ObjectAssociationQuery;
use flipbox\hubspot\HubSpot;
use flipbox\hubspot\records\ObjectAssociation;
use flipbox\hubspot\services\resources\Companies;
use flipbox\hubspot\services\resources\ContactLists;
use flipbox\hubspot\services\resources\Contacts;
use flipbox\hubspot\services\resources\CRUDInterface;
use yii\base\InvalidConfigException;

/**
 * @author Flipbox Factory <hello@flipboxfactory.com>
 * @since 1.0.0
 */
class Objects extends Field
{
    /**
     * The action event name
     */
    const EVENT_REGISTER_ACTIONS = 'registerActions';

    /**
     * The action event name
     */
    const EVENT_REGISTER_AVAILABLE_ACTIONS = 'registerAvailableActions';

    /**
     * The item action event name
     */
    const EVENT_REGISTER_ITEM_ACTIONS = 'registerItemActions';

    /**
     * The item action event name
     */
    const EVENT_REGISTER_AVAILABLE_ITEM_ACTIONS = 'registerAvailableItemActions';

    /**
     * The input template path
     */
    const INPUT_TEMPLATE_PATH = 'hubspot/_components/fieldtypes/Objects/input';

    /**
     * The default HubSpot Resource Id (if none exists)
     */
    const DEFAULT_HUBSPOT_ID = 'UNKNOWN_ID';

    /**
     * @var string
     */
    public $object;

    /**
     * @var int|null
     */
    public $min;

    /**
     * @var int|null
     */
    public $max;

    /**
     * @var string
     */
    public $viewUrl = '';

    /**
     * @var string
     */
    public $listUrl = '';

    /**
     * @var array
     */
    public $selectedActions = [];

    /**
     * @var array
     */
    public $selectedItemActions = [];

    /**
     * @var string|null
     */
    public $selectionLabel;

    /**
     * @inheritdoc
     */
    public static function displayName(): string
    {
        return Craft::t('hubspot', 'HubSpot Objects');
    }

    /**
     * @inheritdoc
     */
    public static function defaultSelectionLabel(): string
    {
        return Craft::t('hubspot', 'Add a HubSpot Objects');
    }

    /**
     * @inheritdoc
     */
    public static function hasContentColumn(): bool
    {
        return false;
    }

    /*******************************************
     * VALIDATION
     *******************************************/

    /**
     * @inheritdoc
     */
    public function getElementValidationRules(): array
    {
        return [
            [
                MinMaxValidator::class,
                'min' => $this->min,
                'max' => $this->max,
                'tooFew' => Craft::t(
                    'hubspot',
                    '{attribute} should contain at least {min, number} {min, plural, one{domain} other{domains}}.'
                ),
                'tooMany' => Craft::t(
                    'hubspot',
                    '{attribute} should contain at most {max, number} {max, plural, one{domain} other{domains}}.'
                ),
                'skipOnEmpty' => false
            ]
        ];
    }

    /*******************************************
     * CRUD
     *******************************************/

    /**
     * @return CRUDInterface
     * @throws InvalidConfigException
     */
    public function getResource(): CRUDInterface
    {
        $service = HubSpot::getInstance()->getResources()->get($this->object);

        if (!$service instanceof CRUDInterface) {
            throw new InvalidConfigException(sprintf(
                "Resource must be an instance of '%s', '%s' given",
                CRUDInterface::class,
                get_class($service)
            ));
        }

        return $service;
    }

    /*******************************************
     * CRITERIA
     *******************************************/

    /**
     * @return array
     */
    protected function getResourceCriteriaMap(): array
    {
        return [
            Companies::HUBSPOT_RESOURCE => function (ObjectAssociation $record = null) {
                return [
                    'class' => CompanyCriteria::class,
                    'id' => $record ? $record->objectId : self::DEFAULT_HUBSPOT_ID
                ];
            },
            Contacts::HUBSPOT_RESOURCE => function (ObjectAssociation $record = null) {
                return [
                    'class' => ContactCriteria::class,
                    'id' => $record ? $record->objectId : self::DEFAULT_HUBSPOT_ID
                ];
            },
            ContactLists::HUBSPOT_RESOURCE => function (ObjectAssociation $record = null) {
                return [
                    'class' => ContactListCriteria::class,
                    'id' => $record ? $record->objectId : self::DEFAULT_HUBSPOT_ID
                ];
            }
        ];
    }

    /**
     * @param ObjectAssociation|null $record
     * @param array $config
     * @return ObjectCriteriaInterface
     * @throws \yii\base\InvalidConfigException
     */
    public function createResourceCriteria(
        ObjectAssociation $record = null,
        array $config = []
    ): ObjectCriteriaInterface {
        $resourceMap = $this->getResourceCriteriaMap();

        $criteria = $resourceMap[$this->object] ?? ObjectCriteria::class;

        // Closure check
        if (is_callable($criteria)) {
            $criteria = call_user_func_array($criteria, ['record' => $record]);
        }

        // Ensure Criteria
        if (!$criteria instanceof ObjectCriteriaInterface) {
            $criteria = \flipbox\ember\helpers\ObjectHelper::create(
                $criteria,
                ObjectCriteriaInterface::class
            );
        }

        if (!$criteria instanceof ObjectCriteriaInterface) {
            $criteria = new ObjectCriteria();
        }

        // TODO - apply the $config to the criteria

        return $criteria;
    }

    /*******************************************
     * VALUE
     *******************************************/

    /**
     * @inheritdoc
     */
    public function normalizeValue($value, ElementInterface $element = null)
    {
        return HubSpot::getInstance()->getObjectsField()->normalizeValue(
            $this,
            $value,
            $element
        );
    }


    /*******************************************
     * ELEMENT
     *******************************************/

    /**
     * @inheritdoc
     */
    public function modifyElementsQuery(ElementQueryInterface $query, $value)
    {
        return HubSpot::getInstance()->getObjectsField()->modifyElementsQuery(
            $this,
            $query,
            $value
        );
    }


    /*******************************************
     * RULES
     *******************************************/

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return array_merge(
            parent::rules(),
            [
                [
                    'object',
                    'required',
                    'message' => Craft::t('hubspot', 'Hubspot Object cannot be empty.')
                ],
                [
                    [
                        'object',
                        'min',
                        'max',
                        'viewUrl',
                        'listUrl',
                        'selectionLabel'
                    ],
                    'safe',
                    'on' => [
                        ModelHelper::SCENARIO_DEFAULT
                    ]
                ]
            ]
        );
    }

    /*******************************************
     * SEARCH
     *******************************************/

    /**
     * @param ObjectAssociationQuery $value
     * @inheritdoc
     */
    public function getSearchKeywords($value, ElementInterface $element): string
    {
        $objects = [];

        foreach ($value->all() as $model) {
            array_push($objects, $model->objectId);
        }

        return parent::getSearchKeywords($objects, $element);
    }

    /*******************************************
     * VIEWS
     *******************************************/

    /**
     * @param ObjectAssociationQuery $value
     * @inheritdoc
     * @throws \Twig_Error_Loader
     * @throws \yii\base\Exception
     */
    public function getInputHtml($value, ElementInterface $element = null): string
    {
        $value->limit(null);
        return HubSpot::getInstance()->getObjectsField()->getInputHtml($this, $value, $element, false);
    }

    /**
     * @inheritdoc
     * @throws \Twig_Error_Loader
     * @throws \yii\base\Exception
     */
    public function getSettingsHtml()
    {
        return HubSpot::getInstance()->getObjectsField()->getSettingsHtml($this);
    }

    /*******************************************
     * EVENTS
     *******************************************/

    /**
     * @inheritdoc
     * @throws \Exception
     */
    public function afterElementSave(ElementInterface $element, bool $isNew)
    {
        /** @var ObjectAssociationQuery $value */
        $value = $element->getFieldValue($this->handle);

        HubSpot::getInstance()->getObjectAssociations()->save($value);

        parent::afterElementSave($element, $isNew);
    }
}
