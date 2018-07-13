<?php

/**
 * @copyright  Copyright (c) Flipbox Digital Limited
 * @license    https://flipboxfactory.com/software/hubspot/license
 * @link       https://www.flipboxfactory.com/software/hubspot/
 */

namespace flipbox\hubspot\transformers\elements;

use craft\base\Element;
use craft\base\ElementInterface;
use flipbox\ember\helpers\SiteHelper;
use flipbox\hubspot\fields\Objects;
use flipbox\hubspot\HubSpot;

/**
 * @author Flipbox Factory <hello@flipboxfactory.com>
 * @since 1.0.0
 */
class ObjectId
{
    /**
     * @var Objects
     */
    protected $field;

    /**
     * @param Objects $field
     * @inheritdoc
     */
    public function __construct(Objects $field)
    {
        $this->field = $field;
    }

    /**
     * @inheritdoc
     * @param Element $data
     * @return string|null
     */
    public function __invoke(ElementInterface $data)
    {
        $objectId = HubSpot::getInstance()->getObjectAssociations()->getQuery([
            'select' => ['objectId'],
            'elementId' => $data->getId(),
            'siteId' => SiteHelper::ensureSiteId($data->siteId),
            'fieldId' => $this->field->id
        ])->scalar();

        if (!is_string($objectId)) {
            HubSpot::warning(sprintf(
                "HubSpot Id association was not found for element '%s'",
                $data->getId()
            ));

            return null;
        }

        HubSpot::info(sprintf(
            "HubSpot Id '%s' was found for element '%s'",
            $objectId,
            $data->getId()
        ));

        return $objectId;
    }
}
