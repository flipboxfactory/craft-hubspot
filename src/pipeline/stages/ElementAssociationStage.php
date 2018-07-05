<?php

/**
 * @copyright  Copyright (c) Flipbox Digital Limited
 * @license    https://flipboxfactory.com/software/hubspot/license
 * @link       https://www.flipboxfactory.com/software/hubspot/
 */

namespace flipbox\hubspot\pipeline\stages;

use craft\base\Element;
use craft\base\ElementInterface;
use craft\helpers\Json;
use flipbox\hubspot\db\ObjectAssociationQuery;
use flipbox\hubspot\fields\Objects;
use flipbox\hubspot\HubSpot;
use flipbox\hubspot\services\resources\Companies;
use flipbox\hubspot\services\resources\Contacts;
use Flipbox\Skeleton\Logger\AutoLoggerTrait;
use League\Pipeline\StageInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Log\InvalidArgumentException;
use yii\base\BaseObject;

/**
 * This stage is intended to associate newly created HubSpot Objects to Craft Elements.
 *
 * @author Flipbox Factory <hello@flipboxfactory.com>
 * @since 1.0.0
 */
class ElementAssociationStage extends BaseObject implements StageInterface
{
    use AutoLoggerTrait;

    /**
     * @var Objects
     */
    private $field;

    /**
     * ElementAssociationStage constructor.
     * @param Objects $field
     * @param array $config
     */
    public function __construct(Objects $field, $config = [])
    {
        $this->field = $field;
        parent::__construct($config);
    }

    /**
     * @param mixed $response
     * @param ElementInterface|null $source
     * @return string|null
     * @throws \Throwable
     */
    public function __invoke($response, ElementInterface $source = null)
    {
        /** @var Element $source */
        if ($source === null) {
            throw new InvalidArgumentException("Source must be an element.");
        }

        /** @var Element $source */
        if (!$response instanceof ResponseInterface) {
            throw new InvalidArgumentException(sprintf(
                "Data must be an instance of '%s'.",
                ResponseInterface::class
            ));
        }

        if (null === $source->getId()) {
            HubSpot::error("The element must have an Id");
            return null;
        }

        if ($source->hasErrors()) {
            HubSpot::error("The element has errors, not associating...");
            return null;
        }

        if (null === ($objectId = $this->getObjectIdFromResponse($response, $this->field->object))) {
            HubSpot::error(sprintf(
                "Unable to identify HubSpot id from payload: %s",
                (string)Json::encode($response)
            ));
            return null;
        }

        if (false === $this->associate($objectId, $source)) {
            throw new InvalidArgumentException(sprintf(
                "Unable to perform save: %s",
                (string)Json::encode($source->getErrors())
            ));
        }

        HubSpot::info(sprintf(
            "Successfully associated object '%s' to element '%s'",
            (string)$objectId,
            $source->getId()
        ));


        return $response;
    }

    /**
     * @param string $objectId
     * @param ElementInterface $element
     * @return bool
     * @throws \Throwable
     */
    protected function associate(string $objectId, ElementInterface $element): bool
    {
        /** @var Element $element */
        $fieldHandle = $this->field->handle;

        /** @var ObjectAssociationQuery $fieldValue */
        if (null === ($fieldValue = $element->{$fieldHandle})) {
            $this->warning("Field is not available on element.");
            return false;
        };

        $associations = $fieldValue->indexBy('objectId')->all();

        if (!array_key_exists($objectId, $associations)) {
            $associations[$objectId] = HubSpot::getInstance()->getObjectAssociations()->create([
                'objectId' => $objectId,
                'elementId' => $element->getId(),
                'fieldId' => $this->field->id,
                'siteId' => $element->siteId
            ]);

            $fieldValue->setCachedResult($associations);

            return HubSpot::getInstance()->getObjectAssociations()->save(
                $fieldValue
            );
        }

        return true;
    }

    /**
     * @param ResponseInterface $response
     * @param string $resource
     * @return string|null
     */
    protected function getObjectIdFromResponse(ResponseInterface $response, string $resource)
    {
        $id = null;

        $data = Json::decodeIfJson(
            $response->getBody()->getContents()
        );

        switch ($resource) {
            case Companies::HUBSPOT_RESOURCE:
                $id = $data['companyId'] ?? null;
                break;

            case Contacts::HUBSPOT_RESOURCE:
                $id = $data['vid'] ?? null;
                break;
        }

        return $id ? (string)$id : null;
    }
}
