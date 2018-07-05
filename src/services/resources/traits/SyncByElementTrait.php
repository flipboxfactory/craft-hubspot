<?php

/**
 * @copyright  Copyright (c) Flipbox Digital Limited
 * @license    https://flipboxfactory.com/software/hubspot/license
 * @link       https://www.flipboxfactory.com/software/hubspot/
 */

namespace flipbox\hubspot\services\resources\traits;

use craft\base\Element;
use craft\base\ElementInterface;
use flipbox\hubspot\connections\ConnectionInterface;
use flipbox\hubspot\fields\Objects;
use flipbox\hubspot\helpers\ConnectionHelper;
use flipbox\hubspot\HubSpot;
use flipbox\hubspot\pipeline\Resource;
use flipbox\hubspot\pipeline\stages\ElementAssociationStage;
use flipbox\hubspot\pipeline\stages\ElementSaveStage;
use flipbox\hubspot\traits\TransformElementIdTrait;
use flipbox\hubspot\traits\TransformElementPayloadTrait;
use Psr\SimpleCache\CacheInterface;

/**
 * @author Flipbox Factory <hello@flipboxfactory.com>
 * @since 1.0.0
 */
trait SyncByElementTrait
{
    use TransformElementIdTrait,
        TransformElementPayloadTrait;

    /**
     * @param string $id
     * @param ConnectionInterface|string|null $connection
     * @param CacheInterface|string|null $cache
     * @return callable
     */
    public abstract function rawHttpReadRelay(
        string $id,
        ConnectionInterface $connection = null,
        CacheInterface $cache = null
    ): callable;

    /**
     * @param array $payload
     * @param string|null $identifier
     * @param ConnectionInterface|string|null $connection
     * @param CacheInterface|string|null $cache
     * @return callable
     * @throws \yii\base\InvalidConfigException
     */
    public abstract function rawHttpUpsertRelay(
        array $payload,
        string $identifier = null,
        ConnectionInterface $connection = null,
        CacheInterface $cache = null
    ): callable;

    /**
     * @param ElementInterface $element
     * @param Objects $field
     * @param ConnectionInterface|null $connection
     * @param CacheInterface|null $cache
     * @return bool
     * @throws \yii\base\InvalidConfigException
     */
    public function syncDown(
        ElementInterface $element,
        Objects $field,
        ConnectionInterface $connection = null,
        CacheInterface $cache = null
    ): bool {
        /** @var Element $element */

        if (null === ($id = $this->transformElementId($element, $field))) {
            return false;
        }

        (new Resource(
            $this->rawHttpReadRelay(
                $id,
                ConnectionHelper::resolveConnection($connection),
                $cache
            ),
            null,
            HubSpot::getInstance()->getPsrLogger()
        ))->build()->pipe(
            new ElementSaveStage($field)
        )->pipe(
            new ElementAssociationStage($field)
        )(null, $element);

        return !$element->hasErrors();
    }

    /**
     * @param ElementInterface $element
     * @param Objects $field
     * @param ConnectionInterface|null $connection
     * @param CacheInterface|null $cache
     * @return bool
     * @throws \yii\base\InvalidConfigException
     */
    public function syncUp(
        ElementInterface $element,
        Objects $field,
        ConnectionInterface $connection = null,
        CacheInterface $cache = null
    ): bool {
        /** @var Element $element */

        (new Resource(
            $this->rawHttpUpsertRelay(
                $this->transformElementPayload($element, $field),
                $this->transformElementId($element, $field),
                $connection,
                $cache
            ),
            null,
            HubSpot::getInstance()->getPsrLogger()
        ))->build()->pipe(
            new ElementAssociationStage($field)
        )(null, $element);

        return !$element->hasErrors();
    }
}
