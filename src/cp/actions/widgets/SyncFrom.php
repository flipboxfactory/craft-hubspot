<?php

/**
 * @copyright  Copyright (c) Flipbox Digital Limited
 * @license    https://flipboxfactory.com/software/hubspot/license
 * @link       https://www.flipboxfactory.com/software/hubspot/
 */

namespace flipbox\craft\hubspot\cp\actions\widgets;

use Craft;
use craft\base\ElementInterface;
use flipbox\craft\ember\helpers\SiteHelper;
use flipbox\craft\hubspot\cp\actions\sync\AbstractSyncFrom;
use flipbox\craft\hubspot\fields\Objects;
use flipbox\craft\hubspot\HubSpot;
use flipbox\craft\hubspot\records\ObjectAssociation;
use flipbox\craft\integration\actions\ResolverTrait;
use flipbox\craft\integration\fields\Integrations;
use flipbox\craft\integration\queries\IntegrationAssociationQuery;
use yii\web\HttpException;

/**
 * @author Flipbox Factory <hello@flipboxfactory.com>
 * @since 1.0.0
 */
class SyncFrom extends AbstractSyncFrom
{
    use ResolverTrait;

    /**
     * @param string $id
     * @param string $field
     * @param string $elementType
     * @param int|null $siteId
     * @return ElementInterface|mixed
     * @throws HttpException
     * @throws \yii\web\UnauthorizedHttpException
     */
    public function run(string $id, string $field, string $elementType, int $siteId = null)
    {
        /** @var Objects $field */
        $field = $this->resolveField($field);

        /** @var ElementInterface $element */
        $element = $this->autoResolveElement($field, $id, $elementType, $siteId);

        return $this->runInternal($element, $field, $id);
    }

    /**
     * @param Integrations $field
     * @param string $id
     * @param string $elementType
     * @param int|null $siteId
     * @return ElementInterface
     */
    private function autoResolveElement(
        Integrations $field,
        string $id,
        string $elementType,
        int $siteId = null
    ): ElementInterface {

        /** @var IntegrationAssociationQuery $query */
        $query = ObjectAssociation::find()
            ->select(['elementId'])
            ->fieldId($field->id)
            ->objectId($id)
            ->siteId(SiteHelper::ensureSiteId($siteId));

        if ($elementId = $query->scalar()) {
            try {
                $element = $this->resolveElement($elementId);
            } catch (HttpException $e) {
                HubSpot::warning(sprintf(
                    "Unable to find element '%s' associated to HubSpot field '%s' Id '%s'",
                    $elementId,
                    $field->handle,
                    $id
                ));
            }
        }

        if (empty($element)) {
            $element = Craft::$app->getElements()->createElement($elementType);
        }

        return $element;
    }
}
