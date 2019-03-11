<?php

/**
 * @copyright  Copyright (c) Flipbox Digital Limited
 * @license    https://flipboxfactory.com/software/hubspot/license
 * @link       https://www.flipboxfactory.com/software/hubspot/
 */

namespace flipbox\craft\hubspot\cp\actions;

use Craft;
use flipbox\craft\ember\actions\models\CreateModel;
use flipbox\craft\hubspot\HubSpot;
use flipbox\craft\hubspot\models\Settings;
use yii\base\Model;

/**
 * @author Flipbox Factory <hello@flipboxfactory.com>
 * @since 1.0.0
 */
class SaveSettings extends CreateModel
{
    /**
     * @return array
     */
    public $validBodyParams = [
        'defaultConnection',
        'defaultCache'
    ];

    /**
     * @inheritdoc
     */
    public $statusCodeSuccess = 200;

    /**
     * @inheritdoc
     * @throws \yii\web\UnauthorizedHttpException
     */
    public function run()
    {
        return $this->runInternal(
            HubSpot::getInstance()->getSettings()
        );
    }

    /**
     * @inheritdoc
     * @param Settings $model
     */
    protected function performAction(Model $model): bool
    {
        return Craft::$app->getPlugins()->savePluginSettings(
            HubSpot::getInstance(),
            $model->toArray(
                $this->validBodyParams()
            )
        );
    }

    /**
     * @inheritdoc
     * @return Settings
     */
    protected function newModel(array $config = []): Model
    {
        return clone HubSpot::getInstance()->getSettings();
    }
}
