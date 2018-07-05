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
use flipbox\hubspot\pipeline\stages\ElementAssociationStage;
use flipbox\hubspot\pipeline\stages\ElementSaveStage;
use flipbox\hubspot\traits\TransformElementIdTrait;
use flipbox\hubspot\traits\TransformElementPayloadTrait;
use flipbox\hubspot\transformers\collections\TransformerCollectionInterface;
use League\Pipeline\PipelineBuilderInterface;
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
     * @param TransformerCollectionInterface|array|null $transformer
     * @return PipelineBuilderInterface
     */
    public abstract function rawReadPipeline(
        string $id,
        ConnectionInterface $connection = null,
        CacheInterface $cache = null,
        TransformerCollectionInterface $transformer = null
    ): PipelineBuilderInterface;

    /**
     * @param array $payload
     * @param string|null $identifier
     * @param ConnectionInterface|string|null $connection
     * @param CacheInterface|string|null $cache
     * @param TransformerCollectionInterface|array|null $transformer
     * @return PipelineBuilderInterface
     */
    public abstract function rawUpsertPipeline(
        array $payload,
        string $identifier = null,
        ConnectionInterface $connection = null,
        CacheInterface $cache = null,
        TransformerCollectionInterface $transformer = null
    ): PipelineBuilderInterface;

    /**
     * @param ElementInterface $element
     * @param Objects $field
     * @param ConnectionInterface|string|null $connection
     * @param CacheInterface|string|null $cache
     * @return bool
     */
    public function syncDown(
        ElementInterface $element,
        Objects $field,
        ConnectionInterface $connection = null,
        CacheInterface $cache = null
    ): bool {
        /** @var Element $element */

        if (null === ($elementId = $this->transformElementId($element, $field))) {
            return false;
        }

        $this->rawReadPipeline(
            $elementId,
            $connection,
            $cache,
            false
        )->build()->pipe(
            new ElementSaveStage($field)
        )->pipe(
            new ElementAssociationStage($field)
        )(null, $element);

        return !$element->hasErrors();
    }

    /**
     * @param ElementInterface $element
     * @param Objects $field
     * @param ConnectionInterface|string|null $connection
     * @param CacheInterface|string|null $cache
     * @return false|string
     */
    public function syncUp(
        ElementInterface $element,
        Objects $field,
        ConnectionInterface $connection = null,
        CacheInterface $cache = null
    ): bool {
        /** @var Element $element */

        $this->rawUpsertPipeline(
            $this->transformElementPayload($element, $field),
            $this->transformElementId($element, $field),
            $connection,
            $cache,
            false
        )->build()->pipe(
            new ElementAssociationStage($field)
        )(null, $element);

        return !$element->hasErrors();
    }
}
