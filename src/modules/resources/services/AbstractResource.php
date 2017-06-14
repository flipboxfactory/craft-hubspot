<?php

namespace flipbox\hubspot\modules\resources\services;

use flipbox\hubspot\HubSpot;
use Flipbox\Transform\Helpers\Transformer as TransformerHelper;
use Flipbox\Transform\Transformers\TransformerInterface;
use flipbox\transformer\Transformer;
use yii\base\Component;

class AbstractResource extends Component
{

    /**
     * @param $transformer
     * @param string      $class
     * @param string      $context
     * @return callable|TransformerInterface|null
     * @throws \Exception
     */
    public function resolveTransformer(
        $transformer = HubSpot::DEFAULT_TRANSFORMER,
        string $class,
        string $context = Transformer::CONTEXT_ARRAY
    ) {
        if (TransformerHelper::isTransformer($transformer)) {
            return $transformer;
        }

        if (TransformerHelper::isTransformerClass($transformer)) {
            return new $transformer();
        }

        if (is_string($transformer)) {
            return Transformer::getInstance()->findTransformer($transformer, $class, 'hubspot', $context);
        }

        throw new \Exception("Invalid transformer");
    }

}
