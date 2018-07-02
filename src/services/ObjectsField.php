<?php

/**
 * @copyright  Copyright (c) Flipbox Digital Limited
 * @license    https://flipboxfactory.com/software/hubspot/license
 * @link       https://www.flipboxfactory.com/software/hubspot/
 */

namespace flipbox\hubspot\services;

use Craft;
use craft\base\ElementInterface;
use craft\base\FieldInterface;
use craft\helpers\Component as ComponentHelper;
use craft\helpers\StringHelper;
use flipbox\craft\sortable\associations\db\SortableAssociationQueryInterface;
use flipbox\craft\sortable\associations\records\SortableAssociationInterface;
use flipbox\craft\sortable\associations\services\SortableFields;
use flipbox\hubspot\db\ObjectAssociationQuery;
use flipbox\hubspot\events\RegisterResourceFieldActionsEvent;
use flipbox\hubspot\fields\actions\ObjectActionInterface;
use flipbox\hubspot\fields\actions\ObjectItemActionInterface;
use flipbox\hubspot\fields\Objects;
use flipbox\hubspot\HubSpot;
use flipbox\hubspot\records\ObjectAssociation;
use flipbox\hubspot\web\assets\objects\Objects as ObjectsFieldAsset;
use yii\base\Exception;

/**
 * @author Flipbox Factory <hello@flipboxfactory.com>
 * @since 1.0.0
 */
class ObjectsField extends SortableFields
{
    /**
     * @inheritdoc
     */
    const SOURCE_ATTRIBUTE = ObjectAssociation::SOURCE_ATTRIBUTE;

    /**
     * @inheritdoc
     */
    const TARGET_ATTRIBUTE = ObjectAssociation::TARGET_ATTRIBUTE;

    /**
     * HubSpot Object Association fields, indexed by their Id.
     *
     * @var Objects[]
     */
    private $fields = [];

    /**
     * @inheritdoc
     */
    protected static function tableAlias(): string
    {
        return ObjectAssociation::tableAlias();
    }

    /**
     * @param int $id
     * @return Objects|null
     */
    public function findById(int $id)
    {
        if (!array_key_exists($id, $this->fields)) {
            $field = Craft::$app->getFields()->getFieldById($id);
            if (!$field instanceof Objects) {
                $field = null;
            }

            $this->fields[$id] = $field;
        }

        return $this->fields[$id];
    }

    /**
     * @inheritdoc
     * @return ObjectAssociationQuery
     * @throws Exception
     */
    protected function getQuery(
        FieldInterface $field,
        ElementInterface $element = null
    ): SortableAssociationQueryInterface {
        $query = $this->baseQuery($field, $element);

        /** @var Objects $field */

        if ($field->max !== null) {
            $query->limit($field->max);
        }

        return $query;
    }

    /**
     * @param FieldInterface $field
     * @param ElementInterface|null $element
     * @return ObjectAssociationQuery
     * @throws Exception
     */
    private function baseQuery(
        FieldInterface $field,
        ElementInterface $element = null
    ): ObjectAssociationQuery {
        /** @var Objects $field */
        $this->ensureField($field);

        $query = HubSpot::getInstance()->getObjectAssociations()->getQuery()
            ->field($field->id)
            ->site($this->targetSiteId($element));

        $query->{ObjectAssociation::SOURCE_ATTRIBUTE} = $element === null ? null : $element->getId();

        return $query;
    }

    /*******************************************
     * NORMALIZE VALUE
     *******************************************/

    /**
     * @inheritdoc
     * @throws Exception
     */
    protected function normalizeQueryInputValue(
        FieldInterface $field,
        $value,
        int &$sortOrder,
        ElementInterface $element = null
    ): SortableAssociationInterface {
        if (is_array($value)) {
            $value = StringHelper::toString($value);
        }

        $query = $this->baseQuery($field, $element)
            ->sortOrder($sortOrder++);

        $query->{ObjectAssociation::TARGET_ATTRIBUTE} = $value;

        return $query;
    }

    /**
     * @param FieldInterface $field
     * @throws Exception
     */
    private function ensureField(FieldInterface $field)
    {
        if (!$field instanceof Objects) {
            throw new Exception(sprintf(
                "The field must be an instance of '%s', '%s' given.",
                (string)Objects::class,
                (string)get_class($field)
            ));
        }
    }


    /**
     * @param Objects $field
     * @param ObjectAssociationQuery $query
     * @param ElementInterface|null $element
     * @param bool $static
     * @return null|string
     * @throws Exception
     * @throws \Twig_Error_Loader
     */
    public function getInputHtml(
        Objects $field,
        ObjectAssociationQuery $query,
        ElementInterface $element = null,
        bool $static = false
    ) {
        Craft::$app->getView()->registerAssetBundle(ObjectsFieldAsset::class);

        return Craft::$app->getView()->renderTemplate(
            $field::INPUT_TEMPLATE_PATH,
            [
                'field' => $field,
                'element' => $element,
                'value' => $query,
                'actions' => $this->getActionHtml($field, $element),
                'itemActions' => $this->getItemActionHtml($field, $element),
                'static' => $static
            ]
        );
    }

    /**
     * @param Objects $field
     * @return null|string
     * @throws Exception
     * @throws \Twig_Error_Loader
     */
    public function getSettingsHtml(
        Objects $field
    ) {
        return Craft::$app->getView()->renderTemplate(
            'hubspot/_components/fieldtypes/Objects/settings',
            [
                'field' => $field
            ]
        );
    }


    /*******************************************
     * ACTIONS
     *******************************************/
    /**
     * @param Objects $field
     * @param ElementInterface|null $element
     * @return ObjectActionInterface[]
     * @throws \craft\errors\MissingComponentException
     * @throws \yii\base\InvalidConfigException
     */
    public function getActions(Objects $field, ElementInterface $element = null): array
    {
        $actions = [];

        $event = new RegisterResourceFieldActionsEvent([
            'actions' => $actions,
            'element' => $element
        ]);

        $field->trigger(
            $field::EVENT_REGISTER_ACTIONS,
            $event
        );

        return $this->resolveActions($event->actions, ObjectActionInterface::class);
    }

    /**
     * @param Objects $field
     * @param ElementInterface|null $element
     * @return ObjectItemActionInterface[]
     * @throws \craft\errors\MissingComponentException
     * @throws \yii\base\InvalidConfigException
     */
    public function getItemActions(Objects $field, ElementInterface $element = null): array
    {
        $event = new RegisterResourceFieldActionsEvent([
            'actions' => [],
            'element' => $element
        ]);

        $field->trigger(
            $field::EVENT_REGISTER_ITEM_ACTIONS,
            $event
        );

        return $this->resolveActions($event->actions, ObjectItemActionInterface::class);
    }

    /**
     * @param array $actions
     * @param string $instance
     * @return array
     * @throws \craft\errors\MissingComponentException
     * @throws \yii\base\InvalidConfigException
     */
    protected function resolveActions(array $actions, string $instance)
    {
        foreach ($actions as $i => $action) {
            // $action could be a string or config array
            if (!$action instanceof $instance) {
                $actions[$i] = $action = ComponentHelper::createComponent($action, $instance);

                if ($actions[$i] === null) {
                    unset($actions[$i]);
                }
            }
        }

        return array_values($actions);
    }

    /**
     * @param Objects $field
     * @param ElementInterface|null $element
     * @return array
     * @throws \craft\errors\MissingComponentException
     * @throws \yii\base\InvalidConfigException
     */
    protected function getActionHtml(Objects $field, ElementInterface $element = null): array
    {
        $actionData = [];

        foreach ($this->getActions($field, $element) as $action) {
            $actionData[] = [
                'type' => get_class($action),
                'destructive' => $action->isDestructive(),
                'params' => [],
                'name' => $action->getTriggerLabel(),
                'trigger' => $action->getTriggerHtml(),
                'confirm' => $action->getConfirmationMessage(),
            ];
        }

        return $actionData;
    }

    /**
     * @param Objects $field
     * @param ElementInterface|null $element
     * @return array
     * @throws \craft\errors\MissingComponentException
     * @throws \yii\base\InvalidConfigException
     */
    protected function getItemActionHtml(Objects $field, ElementInterface $element = null): array
    {
        $actionData = [];

        foreach ($this->getItemActions($field, $element) as $action) {
            $actionData[] = [
                'type' => get_class($action),
                'destructive' => $action->isDestructive(),
                'params' => [],
                'name' => $action->getTriggerLabel(),
                'trigger' => $action->getTriggerHtml(),
                'confirm' => $action->getConfirmationMessage(),
            ];
        }

        return $actionData;
    }
}
