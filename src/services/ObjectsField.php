<?php

/**
 * @copyright  Copyright (c) Flipbox Digital Limited
 * @license    https://flipboxfactory.com/software/hubspot/license
 * @link       https://www.flipboxfactory.com/software/hubspot/
 */

namespace flipbox\hubspot\services;

use Craft;
use flipbox\craft\integration\fields\Integrations;
use flipbox\craft\integration\services\IntegrationAssociations;
use flipbox\craft\integration\services\IntegrationField;
use flipbox\hubspot\fields\actions\SyncItemFrom;
use flipbox\hubspot\fields\actions\SyncItemTo;
use flipbox\hubspot\fields\actions\SyncTo;
use flipbox\hubspot\HubSpot;
use flipbox\hubspot\records\ObjectAssociation;

/**
 * @author Flipbox Factory <hello@flipboxfactory.com>
 * @since 1.0.0
 */
class ObjectsField extends IntegrationField
{
    /**
     * @inheritdoc
     */
    protected $defaultAvailableActions = [
        SyncTo::class
    ];

    /**
     * @inheritdoc
     */
    protected $defaultAvailableItemActions = [
        SyncItemFrom::class,
        SyncItemTo::class,
    ];

    /**
     * @inheritdoc
     * @return ObjectAssociations
     */
    protected function associationService(): IntegrationAssociations
    {
        return HubSpot::getInstance()->getObjectAssociations();
    }

    /**
     * @inheritdoc
     */
    protected static function tableAlias(): string
    {
        return ObjectAssociation::tableAlias();
    }

    /*******************************************
     * SETTINGS
     *******************************************/

    /**
     * @inheritdoc
     */
    protected function settingsHtmlVariables(Integrations $field): array
    {
        return array_merge(
            parent::settingsHtmlVariables($field),
            [
                'objects' => $this->getObjects(),
            ]
        );
    }

    /*******************************************
     * OBJECTS
     *******************************************/

    /**
     * @return array
     */
    protected function getObjects(): array
    {
        return [
            resources\Companies::HUBSPOT_RESOURCE => [
                'label' => Craft::t('hubspot', 'Companies'),
                'value' => resources\Companies::HUBSPOT_RESOURCE
            ],
            resources\Contacts::HUBSPOT_RESOURCE => [
                'label' => Craft::t('hubspot', 'Contacts'),
                'value' => resources\Contacts::HUBSPOT_RESOURCE
            ],
            resources\ContactLists::HUBSPOT_RESOURCE => [
                'label' => Craft::t('hubspot', 'Contact Lists'),
                'value' => resources\ContactLists::HUBSPOT_RESOURCE
            ]
        ];
    }

    /**
     * @inheritdoc
     */
    public function getObjectLabel(Integrations $field): string
    {
        return $this->getObjects()[$field->object]['label'] ?? null;
    }
}
