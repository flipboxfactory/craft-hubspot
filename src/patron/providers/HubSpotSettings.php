<?php

/**
 * @copyright  Copyright (c) Flipbox Digital Limited
 * @license    https://flipboxfactory.com/software/hubspot/license
 * @link       https://www.flipboxfactory.com/software/hubspot/
 */

namespace flipbox\hubspot\patron\providers;

use Craft;
use flipbox\ember\helpers\ModelHelper;
use flipbox\patron\providers\Base;

/**
 * @author Flipbox Factory <hello@flipboxfactory.com>
 * @since 1.0.0
 */
class HubSpotSettings extends Base
{
    /**
     * @var string
     */
    public $defaultScopes;

    /**
     * @return string
     * @throws \Twig_Error_Loader
     * @throws \yii\base\Exception
     */
    public function inputHtml(): string
    {
        return Craft::$app->getView()->renderTemplate(
            'hubspot/_patron/providers/settings',
            [
                'settings' => $this
            ]
        );
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return array_merge(
            parent::rules(),
            [
                [
                    [
                        'defaultScopes'
                    ],
                    'required'
                ],
                [
                    [
                        'defaultScopes'
                    ],
                    'safe',
                    'on' => [
                        ModelHelper::SCENARIO_DEFAULT
                    ]
                ]
            ]
        );
    }
}
