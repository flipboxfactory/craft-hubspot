<?php

/**
 * @copyright  Copyright (c) Flipbox Digital Limited
 * @license    https://flipboxfactory.com/software/hubspot/license
 * @link       https://www.flipboxfactory.com/software/hubspot/
 */

namespace flipbox\craft\hubspot\fields;

use Craft;
use craft\base\Element;
use craft\base\ElementInterface;
use craft\helpers\ArrayHelper;
use flipbox\craft\ember\helpers\SiteHelper;
use flipbox\craft\hubspot\fields\actions\SyncItemFrom;
use flipbox\craft\hubspot\fields\actions\SyncItemTo;
use flipbox\craft\hubspot\fields\actions\SyncTo;
use flipbox\craft\hubspot\helpers\TransformerHelper;
use flipbox\craft\hubspot\HubSpot;
use flipbox\craft\hubspot\records\ObjectAssociation;
use flipbox\craft\hubspot\transformers\PopulateElementErrorsFromResponse;
use flipbox\craft\hubspot\transformers\PopulateElementErrorsFromUpsertResponse;
use flipbox\craft\integration\fields\Integrations;
use flipbox\craft\integration\queries\IntegrationAssociationQuery;
use flipbox\hubspot\connections\ConnectionInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\SimpleCache\CacheInterface;

/**
 * @author Flipbox Factory <hello@flipboxfactory.com>
 * @since 1.0.0
 */
abstract class Objects extends Integrations implements ObjectsFieldInterface
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
     *
     * @deprecated
     */
    public $syncToHubSpotOnMatch = false;

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
     * @param array $payload
     * @param string|null $id
     * @return ResponseInterface
     */
    abstract protected function upsertToHubSpot(
        array $payload,
        string $id = null
    ): ResponseInterface;

    /**
     * @param ResponseInterface $response
     * @return string|null
     */
    abstract protected function getObjectIdFromResponse(ResponseInterface $response);

    /**
     * @inheritdoc
     */
    public static function recordClass(): string
    {
        return ObjectAssociation::class;
    }

    /*******************************************
     * CONNECTION
     *******************************************/

    /**
     * @return ConnectionInterface
     * @throws \flipbox\craft\integration\exceptions\ConnectionNotFound
     */
    public function getConnection(): ConnectionInterface
    {
        return HubSpot::getInstance()->getConnections()->get();
    }

    /*******************************************
     * CACHE
     *******************************************/

    /**
     * @return CacheInterface
     */
    public function getCache(): CacheInterface
    {
        return HubSpot::getInstance()->getCache()->get();
    }


    /*******************************************
     * SYNC TO
     *******************************************/

    /**
     * @inheritdoc
     * @throws \Throwable
     */
    public function syncToHubSpot(
        ElementInterface $element,
        string $objectId = null,
        $transformer = null
    ): bool {
        /** @var Element $element */

        $id = $objectId ?: $this->resolveObjectIdFromElement($element);

        // Get callable used to create payload
        if (null === ($transformer = TransformerHelper::resolveTransformer($transformer))) {
            $transformer = HubSpot::getInstance()->getSettings()->getSyncUpsertPayloadTransformer();
        }

        // Create payload
        $payload = call_user_func_array(
            $transformer,
            [
                $element,
                $this,
                $id
            ]
        );

        $response = $this->upsertToHubSpot($payload, $id);

        return $this->handleSyncToHubSpotResponse(
            $response,
            $element,
            $id
        );
    }

    /*******************************************
     * SYNC FROM
     *******************************************/

    /**
     * @@inheritdoc
     * @throws \Throwable
     * @throws \craft\errors\ElementNotFoundException
     * @throws \yii\base\Exception
     */
    public function syncFromHubSpot(
        ElementInterface $element,
        string $objectId = null,
        $transformer = null
    ): bool {

        $id = $objectId ?: $this->resolveObjectIdFromElement($element);

        if (null === $id) {
            return false;
        }

        $response = $this->readFromHubSpot($id);

        if (($response->getStatusCode() < 200 || $response->getStatusCode() >= 300)) {
            call_user_func_array(
                new PopulateElementErrorsFromResponse(),
                [
                    $response,
                    $element,
                    $this,
                    $id
                ]
            );
            return false;
        }

        // Get callable used to populate element
        if (null === ($transformer = TransformerHelper::resolveTransformer($transformer))) {
            $transformer = HubSpot::getInstance()->getSettings()->getSyncPopulateElementTransformer();
        }

        // Populate element
        call_user_func_array(
            $transformer,
            [
                $response,
                $element,
                $this,
                $id
            ]
        );

        if ($objectId !== null) {
            $this->addAssociation(
                $element,
                $id
            );
        }

        return Craft::$app->getElements()->saveElement($element);
    }


    /**
     * @param ElementInterface|Element $element
     * @param string $id
     * @return bool
     * @throws \Throwable
     */
    protected function addAssociation(
        ElementInterface $element,
        string $id
    ) {
        /** @var IntegrationAssociationQuery $query */
        if (null === ($query = $element->getFieldValue($this->handle))) {
            HubSpot::warning("Field is not available on element.");
            return false;
        };

        $associations = ArrayHelper::index($query->all(), 'objectId');

        if (!array_key_exists($id, $associations)) {
            $associations[$id] = $association = new ObjectAssociation([
                'element' => $element,
                'field' => $this,
                'siteId' => SiteHelper::ensureSiteId($element->siteId),
                'objectId' => $id
            ]);

            $query->setCachedResult(array_values($associations));

            return $association->save();
        }

        return true;
    }

    /**
     * @param ResponseInterface $response
     * @param ElementInterface $element
     * @param string|null $objectId
     * @return bool
     * @throws \Throwable
     */
    protected function handleSyncToHubSpotResponse(
        ResponseInterface $response,
        ElementInterface $element,
        string $objectId = null
    ): bool {

        /** @var Element $element */

        if (!($response->getStatusCode() >= 200 && $response->getStatusCode() <= 299)) {
            call_user_func_array(
                new PopulateElementErrorsFromUpsertResponse(),
                [
                    $response,
                    $element,
                    $this,
                    $objectId
                ]
            );
            return false;
        }

        if (empty($objectId)) {
            if (null === ($objectId = $this->getObjectIdFromResponse($response))) {
                HubSpot::error("Unable to determine object id from response");
                return false;
            };

            return $this->addAssociation($element, $objectId);
        }

        return true;
    }

    /**
     * @param ElementInterface|Element $element
     * @return null|string
     */
    protected function resolveObjectIdFromElement(
        ElementInterface $element
    ) {

        if (!$objectId = ObjectAssociation::find()
            ->select(['objectId'])
            ->elementId($element->getId())
            ->fieldId($this->id)
            ->siteId(SiteHelper::ensureSiteId($element->siteId))
            ->scalar()
        ) {
            HubSpot::warning(sprintf(
                "HubSpot Object Id association was not found for element '%s'",
                $element->getId()
            ));

            return null;
        }

        HubSpot::info(sprintf(
            "HubSpot Object Id '%s' was found for element '%s'",
            $objectId,
            $element->getId()
        ));

        return $objectId;
    }
}
