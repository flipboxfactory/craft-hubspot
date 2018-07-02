<?php

/**
 * @copyright  Copyright (c) Flipbox Digital Limited
 * @license    https://flipboxfactory.com/software/hubspot/license
 * @link       https://www.flipboxfactory.com/software/hubspot/
 */

namespace flipbox\hubspot\transformers;

use craft\base\ElementInterface;
use flipbox\hubspot\HubSpot;
use flipbox\hubspot\transformers\error\Interpret;
use Flipbox\Transform\Factory;
use Flipbox\Transform\Scope;
use Flipbox\Transform\Transformers\AbstractTransformer;
use yii\base\DynamicModel;

/**
 * This transformer will take an API response and create/populate a User element.
 *
 * @author Flipbox Factory <hello@flipboxfactory.com>
 * @since 1.0.0
 */
class DynamicModelError extends AbstractTransformer
{
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
        string $resource = null
    ) {
        if (!is_array($data)) {
            $data = [$data];
        }

        $errors = $this->transformErrors($data);

        if (null !== $source) {
            $this->populateSource($source, $errors);
        }

        $model = new DynamicModel();
        $model->addErrors($errors);

        return $model;
    }

    /**
     * @param $object
     * @param array $errors
     */
    protected function populateSource($object, array $errors)
    {
        if (!is_object($object) || !method_exists($object, 'addErrors')) {
            HubSpot::warning(
                "Unable to populate object errors.",
                __METHOD__
            );

            return;
        }

        /** @noinspection PhpUndefinedMethodInspection */
        $object->addErrors($errors);
    }

    /**
     * @param array $data
     * @return array
     */
    protected function transformErrors(array $data): array
    {
        $errors = Factory::item(
            new Interpret,
            $data
        );

        if (!$errors) {
            $errors = [$errors];
        }

        return array_filter($errors);
    }
}
