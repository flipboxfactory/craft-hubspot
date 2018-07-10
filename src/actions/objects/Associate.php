<?php

/**
 * @copyright  Copyright (c) Flipbox Digital Limited
 * @license    https://flipboxfactory.com/software/hubspot/license
 * @link       https://www.flipboxfactory.com/software/hubspot/
 */

namespace flipbox\hubspot\actions\objects;

use Craft;
use flipbox\hubspot\HubSpot;
use flipbox\hubspot\records\ObjectAssociation;
use flipbox\hubspot\transformers\collections\TransformerCollection;
use yii\base\DynamicModel;
use yii\base\Model;

/**
 * @author Flipbox Factory <hello@flipboxfactory.com>
 * @since 1.0.0
 */
class Associate extends AbstractAssociationAction
{
    /**
     * Validate that the HubSpot Object exists prior to associating
     *
     * @var bool
     */
    public $validate = true;

    /**
     * @param string $field
     * @param string $element
     * @param string $newObjectId
     * @param string|null $objectId
     * @param int|null $siteId
     * @param int|null $sortOrder
     * @return Model
     * @throws \flipbox\ember\exceptions\NotFoundException
     * @throws \yii\web\HttpException
     */
    public function run(
        string $field,
        string $element,
        string $newObjectId,
        string $objectId = null,
        int $siteId = null,
        int $sortOrder = null
    ) {
        // Resolve Field
        $field = $this->resolveField($field);

        // Resolve Element
        if (null === ($sourceElement = Craft::$app->getElements()->getElementById($element))) {
            return $this->handleInvalidElementResponse($element);
        }

        // Resolve Site Id
        if (null === $siteId) {
            $siteId = Craft::$app->getSites()->currentSite->id;
        }

        // Find existing?
        if (!empty($objectId)) {
            $association = HubSpot::getInstance()->getObjectAssociations()->getByCondition([
                'objectId' => $objectId,
                'elementId' => $sourceElement->getId(),
                'fieldId' => $field->id,
                'siteId' => $siteId,
            ]);
        } else {
            $association = HubSpot::getInstance()->getObjectAssociations()->create([
                'elementId' => $sourceElement->getId(),
                'fieldId' => $field->id,
                'siteId' => $siteId,

            ]);
        }

        $association->objectId = $newObjectId;
        $association->sortOrder = $sortOrder;

        return $this->runInternal($association);
    }

    /**
     * @inheritdoc
     * @param ObjectAssociation $model
     * @throws \flipbox\ember\exceptions\RecordNotFoundException
     * @throws \Exception
     */
    protected function performAction(Model $model): bool
    {
        if (true === $this->ensureAssociation($model)) {
            if ($this->validate === true && !$this->validateResource($model)) {
                return false;
            }

            return HubSpot::getInstance()->getObjectAssociations()->associate(
                $model
            );
        }

        return false;
    }

    /**
     * @param ObjectAssociation $record
     * @return bool
     * @throws \Exception
     */
    protected function validateResource(
        ObjectAssociation $record
    ): bool {

        if (null === ($fieldId = $record->fieldId)) {
            return false;
        }

        if (null === ($field = HubSpot::getInstance()->getObjectsField()->findById($fieldId))) {
            return false;
        }

        $criteria = $field->getResource()->getAccessorCriteria([
            'id' => $record->objectId,
            'transformer' => TransformerCollection::class
        ]);

        /** @var DynamicModel $response */
        $response = $field->getResource()->read($criteria);

        return !$response->hasErrors();
    }
}
