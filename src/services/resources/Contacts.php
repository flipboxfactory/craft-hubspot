<?php

/**
 * @copyright  Copyright (c) Flipbox Digital Limited
 * @license    https://flipboxfactory.com/software/hubspot/license
 * @link       https://www.flipboxfactory.com/software/hubspot/
 */

namespace flipbox\hubspot\services\resources;

use craft\base\Element;
use craft\base\ElementInterface;
use craft\helpers\ArrayHelper;
use craft\helpers\Json;
use flipbox\hubspot\builders\ContactBuilder;
use flipbox\hubspot\builders\ObjectBuilderInterface;
use flipbox\hubspot\connections\ConnectionInterface;
use flipbox\hubspot\criteria\ContactCriteria;
use flipbox\hubspot\criteria\ObjectCriteriaInterface;
use flipbox\hubspot\fields\Objects;
use flipbox\hubspot\HubSpot;
use flipbox\hubspot\pipeline\Resource;
use flipbox\hubspot\pipeline\stages\ElementAssociationStage;
use flipbox\hubspot\transformers\collections\DynamicTransformerCollection;
use flipbox\hubspot\transformers\collections\TransformerCollectionInterface;
use flipbox\hubspot\transformers\DynamicModelSuccess;
use Flipbox\Relay\HubSpot\Builder\Resources\Contact\Create;
use Flipbox\Relay\HubSpot\Builder\Resources\Contact\Delete;
use Flipbox\Relay\HubSpot\Builder\Resources\Contact\ReadById;
use Flipbox\Relay\HubSpot\Builder\Resources\Contact\Update;
use Psr\SimpleCache\CacheInterface;
use yii\base\Component;

/**
 * @author Flipbox Factory <hello@flipboxfactory.com>
 * @since 1.0.0
 */
class Contacts extends Component implements CRUDInterface
{
    use traits\SyncByElementTrait,
        traits\ReadObjectTrait,
        traits\UpsertObjectTrait,
        traits\DeleteObjectTrait;

    /**
     * The HubSpot Resource name
     */
    const HUBSPOT_RESOURCE = 'contacts';

    /**
     * @return array
     */
    public static function defaultTransformer()
    {
        return [
            'class' => DynamicTransformerCollection::class,
            'handle' => self::HUBSPOT_RESOURCE,
            'transformers' => [
                TransformerCollectionInterface::SUCCESS_KEY => [
                    'class' => DynamicModelSuccess::class,
                    'resource' => self::HUBSPOT_RESOURCE
                ]
            ]
        ];
    }

    /**
     * @param array $config
     * @return ObjectCriteriaInterface
     */
    public function getCriteria(array $config = []): ObjectCriteriaInterface
    {
        return new ContactCriteria($config);
    }

    /**
     * @param array $config
     * @return ObjectBuilderInterface
     */
    public function getBuilder(array $config = []): ObjectBuilderInterface
    {
        return new ContactBuilder($config);
    }

    /**
     * @inheritdoc
     */
    protected static function createRelayBuilderClass(): string
    {
        return Create::class;
    }

    /**
     * @inheritdoc
     */
    protected static function readRelayBuilderClass(): string
    {
        return ReadById::class;
    }

    /**
     * @inheritdoc
     */
    protected static function updateRelayBuilderClass(): string
    {
        return Update::class;
    }

    /**
     * @inheritdoc
     */
    protected static function deleteRelayBuilderClass(): string
    {
        return Delete::class;
    }

    /*******************************************
     * SYNC
     *******************************************/

    /**
     * @inheritdoc
     * @throws \yii\base\InvalidConfigException
     */
    public function syncUp(
        ElementInterface $element,
        Objects $field,
        ConnectionInterface $connection = null,
        CacheInterface $cache = null
    ): bool {
        /** @var Element $element */
        $httpResponse = $this->rawHttpUpsert(
            $this->transformElementPayload($element, $field),
            $this->transformElementId($element, $field),
            $connection,
            $cache
        );

        if ($httpResponse->getStatusCode() === 409) {
            $data = Json::decodeIfJson(
                $httpResponse->getBody()->getContents()
            );

            $contactId = ArrayHelper::getValue($data, 'identityProfile.vid');

            if (!HubSpot::getInstance()->getObjectAssociations()->associateByIds(
                $contactId,
                $element->getId(),
                $field->id,
                $element->siteId
            )) {
                return false;
            }

            if ($field->syncToHubSpotOnMatch === true) {
                return $this->syncUp(
                    $element,
                    $field,
                    $connection,
                    $cache
                );
            }

            return true;
        }

        (new Resource(
            function () use ($httpResponse) {
                return $httpResponse;
            },
            null,
            HubSpot::getInstance()->getPsrLogger()
        ))->build()->pipe(
            new ElementAssociationStage($field)
        )(null, $element);

        return !$element->hasErrors();
    }
}
