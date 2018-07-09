<?php

/**
 * @copyright  Copyright (c) Flipbox Digital Limited
 * @license    https://flipboxfactory.com/software/hubspot/license
 * @link       https://www.flipboxfactory.com/software/hubspot/
 */

namespace flipbox\hubspot\criteria;

use craft\base\ElementInterface;
use flipbox\hubspot\fields\Objects;
use flipbox\hubspot\HubSpot;
use yii\base\BaseObject;

/**
 * @author Flipbox Factory <hello@flipboxfactory.com>
 * @since 1.0.0
 */
class ContactListContactsMutator extends BaseObject implements ObjectMutatorInterface
{
    use traits\TransformerCollectionTrait,
        traits\ConnectionTrait,
        traits\CacheTrait;

    /**
     * @var string
     */
    public $id;

    /**
     * @var array
     */
    public $vids = [];

    /**
     * @var array
     */
    public $emails = [];

    /**
     * @return string
     */
    public function getId(): string
    {
        return (string)$this->id;
    }

    /**
     * @return array
     */
    public function getPayload(): array
    {
        return array_filter(
            [
                'vids' => array_filter($this->vids),
                'emails' => array_filter($this->emails)
            ]
        );
    }

    /**
     * @param ElementInterface $element
     * @param Objects $field
     */
    public function addElement(ElementInterface $element, Objects $field)
    {
        $objectId = HubSpot::getInstance()->getObjectAssociations()->findObjectIdByElement(
            $element,
            $field
        );

        if ($objectId !== null) {
            $this->vids[] = $objectId;
            return;
        }

        if (null !== ($email = $element['email'] ?? null)) {
            $this->emails[] = $email;
            return;
        }
    }
}
