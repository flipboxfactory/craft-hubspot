<?php

/**
 * @copyright  Copyright (c) Flipbox Digital Limited
 * @license    https://flipboxfactory.com/software/hubspot/license
 * @link       https://www.flipboxfactory.com/software/hubspot/
 */

namespace flipbox\hubspot\transformers;

use craft\base\ElementInterface;
use flipbox\flux\helpers\TransformerHelper;
use flipbox\hubspot\HubSpot;
use Flipbox\Transform\Factory;
use Flipbox\Transform\Scope;
use Flipbox\Transform\Transformers\AbstractTransformer;
use yii\base\DynamicModel;

/**
 * @author Flipbox Factory <hello@flipboxfactory.com>
 * @since 1.0.0
 */
class DynamicModelSuccess extends AbstractTransformer
{
    /**
     * The HubSpot resource
     *
     * @var string
     */
    public $resource;

    /**
     * @param $data
     * @param Scope $scope
     * @param string|null $identifier
     * @param ElementInterface|null $source
     * @param string|null $resource
     * @return mixed
     */
    public function __invoke(
        $data,
        Scope $scope,
        string $identifier = null,
        ElementInterface $source = null,
        $resource = null
    ) {
        if (!is_array($data)) {
            $data = [$data];
        }

        if (!$source instanceof ElementInterface) {
            HubSpot::warning(
                "Unable to populate element because an element 'source' does not exist.",
                __METHOD__
            );

            return $this->transform($data);
        }

        $this->populateSource($source, $data, $resource ?: $this->resource);

        return $this->transform($data);
    }

    /**
     * @param ElementInterface $element
     * @param array $data
     * @param string|null $resource
     */
    protected function populateSource(ElementInterface $element, array $data, $resource = null)
    {
        $event = ['populate'];

        if (null !== $resource) {
            array_unshift($event, $resource);
        }

        $event = TransformerHelper::eventName($event);
        $class = get_class($element);

        if (null === ($transformer = HubSpot::getInstance()->getTransformers()->find($event, $class))) {
            HubSpot::warning(
                sprintf(
                    "Populate element '%s' transformer could not be found for event '%s'",
                    $class,
                    $event
                ),
                __METHOD__
            );

            return;
        }

        HubSpot::info(
            sprintf(
                "Populate element '%s' with transformer event '%s'",
                $class,
                $event
            ),
            __METHOD__
        );

        Factory::item(
            $transformer,
            $data,
            [],
            ['source' => $element, 'sObject' => $resource]
        );
    }

    /**
     * @param array $data
     * @return DynamicModel
     */
    protected function transform(array $data): DynamicModel
    {
        return new DynamicModel(array_keys($data), $data);
    }
}
