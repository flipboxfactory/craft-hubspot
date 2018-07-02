<?php

/**
 * @copyright  Copyright (c) Flipbox Digital Limited
 * @license    https://flipboxfactory.com/software/hubspot/license
 * @link       https://www.flipboxfactory.com/software/hubspot/
 */

namespace flipbox\hubspot\transformers\collections;

use flipbox\hubspot\transformers\DynamicModelError;
use flipbox\hubspot\transformers\DynamicModelSuccess;
use yii\base\BaseObject;

/**
 * @author Flipbox Factory <hello@flipboxfactory.com>
 * @since 1.0.0
 */
class TransformerCollection extends BaseObject implements TransformerCollectionInterface
{
    use TransformerCollectionTrait;

    /**
     * @inheritdoc
     */
    public function init()
    {
        parent::init();

        $this->transformers = [
            TransformerCollectionInterface::SUCCESS_KEY => [
                'class' => DynamicModelSuccess::class
            ],
            TransformerCollectionInterface::ERROR_KEY => [
                'class' => DynamicModelError::class
            ]
        ];
    }
}
