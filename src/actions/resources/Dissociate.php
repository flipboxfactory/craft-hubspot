<?php

/**
 * @copyright  Copyright (c) Flipbox Digital Limited
 * @license    https://flipboxfactory.com/software/hubspot/license
 * @link       https://www.flipboxfactory.com/software/hubspot/
 */

namespace flipbox\hubspot\actions\resources;

use Craft;
use flipbox\hubspot\HubSpot;
use flipbox\hubspot\records\ObjectAssociation;
use yii\base\Model;
use yii\web\HttpException;

/**
 * @author Flipbox Factory <hello@flipboxfactory.com>
 * @since 1.0.0
 */
class Dissociate extends AbstractAssociationAction
{
    /**
     * @param string $field
     * @param string $element
     * @param string $objectId
     * @param int|null $siteId
     * @return mixed
     * @throws HttpException
     */
    public function run(
        string $field,
        string $element,
        string $objectId,
        int $siteId = null
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
        return $this->runInternal(HubSpot::getInstance()->getObjectAssociations()->create([
            'objectId' => $objectId,
            'elementId' => $sourceElement->getId(),
            'fieldId' => $field->id,
            'siteId' => $siteId,
        ]));
    }

    /**
     * @inheritdoc
     * @param ObjectAssociation $model
     * @throws \flipbox\ember\exceptions\RecordNotFoundException
     * @throws \yii\db\Exception
     */
    protected function performAction(Model $model): bool
    {
        if (true === $this->ensureAssociation($model)) {
            return HubSpot::getInstance()->getObjectAssociations()->dissociate(
                $model
            );
        }

        return false;
    }
}
