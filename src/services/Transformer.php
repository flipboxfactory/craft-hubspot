<?php

namespace flipbox\hubspot\services;

use flipbox\hubspot\HubSpot;
use Flipbox\Transform\Transformers\TransformerInterface;
use Flipbox\Transform\Helpers\Transformer as TransformerHelper;
use yii\base\Component;
use flipbox\transformer\Transformer as TransformerPlugin;

class Transformer extends Component
{
    /**
     * @param $transformer
     * @param string      $class
     * @param string      $context
     * @return callable|TransformerInterface|string
     * @throws \Exception
     */
    public function get(
        string $class,
        string $context = TransformerPlugin::CONTEXT_ARRAY,
        $transformer = HubSpot::DEFAULT_TRANSFORMER
    ) {
        if (TransformerHelper::isTransformer($transformer)) {
            return $transformer;
        }

        if (TransformerHelper::isTransformerClass($transformer)) {
            return new $transformer();
        }

        if (is_string($transformer)) {
            return TransformerPlugin::getInstance()->findTransformer($transformer, $class, 'hubspot', $context);
        }

        throw new \Exception("Invalid transformer");
    }
}
