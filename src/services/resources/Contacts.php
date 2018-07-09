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
use flipbox\hubspot\criteria\ContactBatchMutatorInterface;
use flipbox\hubspot\criteria\ContactMutator;
use flipbox\hubspot\criteria\ObjectMutatorInterface;
use flipbox\hubspot\connections\ConnectionInterface;
use flipbox\hubspot\criteria\ContactAccessor;
use flipbox\hubspot\criteria\ObjectAccessorInterface;
use flipbox\hubspot\fields\Objects;
use flipbox\hubspot\helpers\ConnectionHelper;
use flipbox\hubspot\HubSpot;
use flipbox\hubspot\pipeline\Resource;
use flipbox\hubspot\pipeline\stages\ElementAssociationStage;
use flipbox\hubspot\transformers\collections\DynamicTransformerCollection;
use flipbox\hubspot\transformers\collections\TransformerCollectionInterface;
use flipbox\hubspot\transformers\DynamicModelSuccess;
use Flipbox\Relay\Builder\RelayBuilderInterface;
use Flipbox\Relay\HubSpot\Builder\Resources\Contact\Batch;
use Flipbox\Relay\HubSpot\Builder\Resources\Contact\Create;
use Flipbox\Relay\HubSpot\Builder\Resources\Contact\Delete;
use Flipbox\Relay\HubSpot\Builder\Resources\Contact\ReadById;
use Flipbox\Relay\HubSpot\Builder\Resources\Contact\Update;
use Psr\Http\Message\ResponseInterface;
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
     * @return ObjectAccessorInterface
     */
    public function getAccessorCriteria(array $config = []): ObjectAccessorInterface
    {
        return new ContactAccessor($config);
    }

    /**
     * @param array $config
     * @return ObjectMutatorInterface
     */
    public function getMutatorCriteria(array $config = []): ObjectMutatorInterface
    {
        return new ContactMutator($config);
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

    /**
     * @inheritdoc
     */
    protected static function batchRelayBuilderClass(): string
    {
        return Batch::class;
    }

    /*******************************************
     * SYNC (OVERRIDE)
     *******************************************/

    /**
     * @param ElementInterface $element
     * @param Objects $field
     * @param array $payload
     * @param string $id
     * @param ConnectionInterface|null $connection
     * @param CacheInterface|null $cache
     * @return bool
     * @throws \yii\base\InvalidConfigException
     */
    public function rawSyncUp(
        ElementInterface $element,
        Objects $field,
        array $payload,
        string $id = null,
        ConnectionInterface $connection = null,
        CacheInterface $cache = null
    ): bool {
        /** @var Element $element */
        $httpResponse = $this->rawHttpUpsert(
            $payload,
            $id,
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

    /*******************************************
     * BATCH
     *******************************************/


    /**
     * @param ContactBatchMutatorInterface $criteria
     * @return ResponseInterface
     * @throws \yii\base\InvalidConfigException
     */
    public function httpBatch(
        ContactBatchMutatorInterface $criteria
    ): ResponseInterface {
        return $this->rawHttpBatch(
            $criteria->getPayload(),
            $criteria->getConnection()
        )();
    }

    /**
     * @param array $payload
     * @param ConnectionInterface|string|null $connection
     * @return ResponseInterface
     * @throws \yii\base\InvalidConfigException
     */
    public function rawHttpBatch(
        array $payload,
        ConnectionInterface $connection = null
    ): ResponseInterface {
        return $this->rawHttpBatchRelay(
            $payload,
            $connection
        )();
    }

    /**
     * @param ContactBatchMutatorInterface $criteria
     * @return callable
     * @throws \yii\base\InvalidConfigException
     */
    public function httpBatchRelay(
        ContactBatchMutatorInterface $criteria
    ): callable {
        return $this->rawHttpBatchRelay(
            $criteria->getPayload(),
            $criteria->getConnection()
        );
    }

    /**
     * @param array $payload
     * @param ConnectionInterface|string|null $connection
     * @return callable
     * @throws \yii\base\InvalidConfigException
     */
    public function rawHttpBatchRelay(
        array $payload,
        ConnectionInterface $connection = null
    ): callable {
        $class = static::batchRelayBuilderClass();

        /** @var RelayBuilderInterface $builder */
        $builder = new $class(
            $payload,
            ConnectionHelper::resolveConnection($connection),
            HubSpot::getInstance()->getPsrLogger()
        );

        return $builder->build();
    }
}
