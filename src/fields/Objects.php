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
use flipbox\hubspot\connections\ConnectionInterface;
use flipbox\hubspot\db\ObjectAssociationQuery;
use flipbox\hubspot\HubSpot;
use flipbox\hubspot\services\resources\CRUDInterface;
use Psr\SimpleCache\CacheInterface;
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
     * Indicates whether the full sync operation should be preformed if a matching HubSpot Object was found but not
     * currently associated to the element.  For example, when attempting to Sync a Craft User to a HubSpot Contact, if
     * the HubSpot Contact already exists; true would override data in HubSpot while false would just perform
     * an association (note, a subsequent sync operation could be preformed)
     * @var bool
     */
    public $syncToHubSpotOnMatch = false;

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
                'min' => $this->min ? (int)$this->min : null,
                'max' => $this->max ? (int)$this->max : null,
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
     * CONNECTION
     *******************************************/

    /**
     * @return ConnectionInterface
     * @throws \yii\base\InvalidConfigException
     */
    public function getConnection(): ConnectionInterface
    {
        $service = HubSpot::getInstance()->getConnections();
        return $service->get($service::DEFAULT_CONNECTION);
    }

    /*******************************************
     * CACHE
     *******************************************/

    /**
     * @return CacheInterface
     * @throws \yii\base\InvalidConfigException
     */
    public function getCache(): CacheInterface
    {
        $service = HubSpot::getInstance()->getCache();
        return $service->get($service::DEFAULT_CACHE);
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
