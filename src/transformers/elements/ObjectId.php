<?php

/**
 * @copyright  Copyright (c) Flipbox Digital Limited
 * @license    https://flipboxfactory.com/software/hubspot/license
 * @link       https://www.flipboxfactory.com/software/hubspot/
 */

namespace flipbox\hubspot\transformers\elements;

use craft\base\Element;
use craft\base\ElementInterface;
use craft\helpers\Json;
use flipbox\ember\helpers\SiteHelper;
use flipbox\hubspot\fields\Objects;
use flipbox\hubspot\HubSpot;
use Flipbox\Transform\Transformers\AbstractSimpleTransformer;

/**
 * @author Flipbox Factory <hello@flipboxfactory.com>
 * @since 1.0.0
 */
class ObjectId extends AbstractSimpleTransformer
{
    /**
     * @var Objects
     */
    protected $field;

    /**
     * @param Objects $field
     * @inheritdoc
     */
    public function __construct(Objects $field, $config = [])
    {
        $this->field = $field;
        parent::__construct($config);
    }

    /**
     * @inheritdoc
     * @return string|null
     */
    public function __invoke($data, string $identifier = null)
    {
        if ($data instanceof ElementInterface) {
            return $this->transformerElementToId($data);
        }

        HubSpot::warning(sprintf(
            "Unable to determine HubSpot Id because data is not an element: %s",
            Json::encode($data)
        ));

        return null;
    }

    /**
     * @param Element|ElementInterface $element
     * @return null|string
     */
    protected function transformerElementToId(ElementInterface $element)
    {
        $objectId = HubSpot::getInstance()->getObjectAssociations()->getQuery([
            'select' => ['objectId'],
            'elementId' => $element->getId(),
            'siteId' => SiteHelper::ensureSiteId($element->siteId),
            'fieldId' => $this->field->id
        ])->scalar();

        if (!is_string($objectId)) {
            HubSpot::warning(sprintf(
                "HubSpot Id association was not found for element '%s'",
                $element->getId()
            ));

            return null;
        }

        HubSpot::info(sprintf(
            "HubSpot Id '%s' was found for element '%s'",
            $objectId,
            $element->getId()
        ));

        return $objectId;
    }
}
