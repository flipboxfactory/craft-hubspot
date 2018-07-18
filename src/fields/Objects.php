<?php

/**
 * @copyright  Copyright (c) Flipbox Digital Limited
 * @license    https://flipboxfactory.com/software/hubspot/license
 * @link       https://www.flipboxfactory.com/software/hubspot/
 */

namespace flipbox\hubspot\fields;

use Craft;
use flipbox\craft\integration\fields\Integrations;
use flipbox\craft\integration\services\IntegrationAssociations;
use flipbox\craft\integration\services\IntegrationField;
use flipbox\hubspot\connections\ConnectionInterface;
use flipbox\hubspot\HubSpot;
use flipbox\hubspot\services\ObjectAssociations;
use flipbox\hubspot\services\ObjectsField;
use flipbox\hubspot\services\resources\CRUDInterface;
use Psr\SimpleCache\CacheInterface;
use yii\base\InvalidConfigException;

/**
 * @author Flipbox Factory <hello@flipboxfactory.com>
 * @since 1.0.0
 */
class Objects extends Integrations
{
    /**
     * @inheritdoc
     */
    const TRANSLATION_CATEGORY = 'hubspot';

    /**
     * @inheritdoc
     */
    const INPUT_TEMPLATE_PATH = 'hubspot/_components/fieldtypes/Objects/input';

    /**
     * @inheritdoc
     */
    const INPUT_ITEM_TEMPLATE_PATH = 'hubspot/_components/fieldtypes/Objects/_inputItem';

    /**
     * @inheritdoc
     */
    const SETTINGS_TEMPLATE_PATH = 'hubspot/_components/fieldtypes/Objects/settings';

    /**
     * @inheritdoc
     */
    const ACTION_PREFORM_ACTION_PATH = 'hubspot/cp/fields/perform-action';

    /**
     * @inheritdoc
     */
    const ACTION_CREATE_ITEM_PATH = 'hubspot/cp/fields/create-item';

    /**
     * @inheritdoc
     */
    const ACTION_ASSOCIATION_ITEM_PATH = 'hubspot/cp/objects/associate';

    /**
     * @inheritdoc
     */
    const ACTION_DISSOCIATION_ITEM_PATH = 'hubspot/cp/objects/dissociate';

    /**
     * @inheritdoc
     */
    const ACTION_PREFORM_ITEM_ACTION_PATH = 'hubspot/cp/fields/perform-item-action';

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
     * @return ObjectsField
     */
    protected function fieldService(): IntegrationField
    {
        return HubSpot::getInstance()->getObjectsField();
    }

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
}
