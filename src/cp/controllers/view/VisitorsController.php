<?php

/**
 * @copyright  Copyright (c) Flipbox Digital Limited
 * @license    https://flipboxfactory.com/software/hubspot/license
 * @link       https://www.flipboxfactory.com/software/hubspot/
 */

namespace flipbox\craft\hubspot\cp\controllers\view;

use craft\helpers\UrlHelper;
use flipbox\craft\hubspot\HubSpot;
use flipbox\craft\hubspot\records\Visitor;
use yii\web\Response;

/**
 * @author Flipbox Factory <hello@flipboxfactory.com>
 * @since 1.1.0
 */
class VisitorsController extends AbstractController
{
    /**
     * The template base path
     */
    const TEMPLATE_BASE = parent::TEMPLATE_BASE . '/visitors';

    /**
     * The index view template path
     */
    const TEMPLATE_INDEX = self::TEMPLATE_BASE . '/index';

    /**
     * The detail view template path
     */
    const TEMPLATE_DETAIL = self::TEMPLATE_BASE . '/detail';

    /**
     * @return Response
     */
    public function actionIndex(): Response
    {
        $variables = [];
        $this->baseVariables($variables);

        $variables['visitors'] = Visitor::find()->all();

        return $this->renderTemplate(
            static::TEMPLATE_INDEX,
            $variables
        );
    }

    /**
     * @param $identifier
     * @return Response
     * @throws \flipbox\craft\ember\exceptions\RecordNotFoundException
     */
    public function actionDetail($identifier): Response
    {
        $visitor = Visitor::getOne($identifier);

        $variables = [];

        $this->baseUpsertVariables($visitor, $variables);

        // Full page form in the CP
        $variables['fullPageForm'] = true;

        $variables['visitor'] = $visitor;
        $variables['statusOptions'] = [
            [
                'label' => HubSpot::t("Successful"),
                'value' => Visitor::STATUS_SUCCESSFUL
            ],
            [
                'label' => HubSpot::t("Pending"),
                'value' => Visitor::STATUS_PENDING
            ],
            [
                'label' => HubSpot::t("Not Found"),
                'value' => Visitor::STATUS_NOT_FOUND
            ],
            [
                'label' => HubSpot::t("Error"),
                'value' => Visitor::STATUS_ERROR
            ]
        ];

        return $this->renderTemplate(
            static::TEMPLATE_DETAIL,
            $variables
        );
    }


    /*******************************************
     * BASE PATHS
     *******************************************/

    /**
     * @return string
     */
    protected function getBaseActionPath(): string
    {
        return parent::getBaseActionPath() . '/visitors';
    }

    /**
     * @return string
     */
    protected function getBaseCpPath(): string
    {
        return parent::getBaseCpPath() . '/visitors';
    }

    /*******************************************
     * VARIABLES
     *******************************************/

    /**
     * @param array $variables
     * @param Visitor $visitor
     */
    protected function baseUpsertVariables(Visitor $visitor, array &$variables = [])
    {
        $this->baseVariables($variables);

        // Breadcrumbs
        $variables['crumbs'][] = [
            'label' => $visitor->token,
            'url' => ''
        ];

        $variables['title'] .= ': ' . $visitor->token;
    }

    /**
     * @inheritdoc
     */
    protected function baseVariables(array &$variables = [])
    {
        parent::baseVariables($variables);

        $title = HubSpot::t("Visitors");
        $variables['title'] .= ' ' . $title;

        // Breadcrumbs
        $variables['crumbs'][] = [
            'label' => $title,
            'url' => UrlHelper::url($this->getBaseCpPath())
        ];
    }
}
