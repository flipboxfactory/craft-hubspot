<?php

/**
 * @copyright  Copyright (c) Flipbox Digital Limited
 * @license    https://flipboxfactory.com/software/hubspot/license
 * @link       https://www.flipboxfactory.com/software/hubspot/
 */

namespace flipbox\hubspot\services\resources;

use flipbox\hubspot\builders\ContactBuilder;
use flipbox\hubspot\builders\ObjectBuilderInterface;
use flipbox\hubspot\criteria\ContactCriteria;
use flipbox\hubspot\criteria\ObjectCriteriaInterface;
use flipbox\hubspot\transformers\collections\TransformerCollectionInterface;
use flipbox\hubspot\transformers\DynamicModelSuccess;
use Flipbox\Relay\HubSpot\Builder\Resources\Contact\Create;
use Flipbox\Relay\HubSpot\Builder\Resources\Contact\Delete;
use Flipbox\Relay\HubSpot\Builder\Resources\Contact\ReadById;
use Flipbox\Relay\HubSpot\Builder\Resources\Contact\Update;
use yii\base\Component;

/**
 * @author Flipbox Factory <hello@flipboxfactory.com>
 * @since 1.0.0
 */
class Contacts extends Component implements CRUDInterface
{
    use traits\SyncByElementTrait,
        traits\ReadObjectTrait,
        traits\UpsertObjectTrait,
        traits\DeleteObjectTrait;

    /**
     * The HubSpot Resource name
     */
    const HUBSPOT_RESOURCE = 'contacts';

    /**
     * @return array
     */
    public static function defaultTransformer()
    {
        return [
            'class' => DynamicTransformerCollection::class,
            'handle' => self::HUBSPOT_RESOURCE,
            'transformers' => [
                TransformerCollectionInterface::SUCCESS_KEY => [
                    'class' => DynamicModelSuccess::class,
                    'resource' => self::HUBSPOT_RESOURCE
                ]
            ]
        ];
    }

    /**
     * @param array $config
     * @return ObjectCriteriaInterface
     */
    public function getCriteria(array $config = []): ObjectCriteriaInterface
    {
        return new ContactCriteria($config);
    }

    /**
     * @param array $config
     * @return ObjectBuilderInterface
     */
    public function getBuilder(array $config = []): ObjectBuilderInterface
    {
        return new ContactBuilder($config);
    }

    /**
     * @inheritdoc
     */
    protected static function createRelayBuilderClass(): string
    {
        return Create::class;
    }

    /**
     * @inheritdoc
     */
    protected static function readRelayBuilderClass(): string
    {
        return ReadById::class;
    }

    /**
     * @inheritdoc
     */
    protected static function updateRelayBuilderClass(): string
    {
        return Update::class;
    }

    /**
     * @inheritdoc
     */
    protected static function deleteRelayBuilderClass(): string
    {
        return Delete::class;
    }
    /*******************************************
     * ELEMENT SYNC JOBS
     *******************************************/

//    /**
//     * @param ElementInterface $element
//     * @param Objects $field
//     * @return null|string
//     */
//    public function createElementSyncToJob(
//        ElementInterface $element,
//        Objects $field
//    ) {
//        return Craft::$app->getQueue()->push(new SyncElementTo([
//            'element' => $element,
//            'field' => $field,
//            'resource' => self::HUBSPOT_RESOURCE
//        ]));
//    }
//
//    /**
//     * @param ElementInterface $element
//     * @param Objects $field
//     * @return null|string
//     */
//    public function createElementSyncFromJob(
//        ElementInterface $element,
//        Objects $field
//    ) {
//        return Craft::$app->getQueue()->push(new SyncElementFrom([
//            'element' => $element,
//            'field' => $field,
//            'resource' => self::HUBSPOT_RESOURCE
//        ]));
//    }

    /*******************************************
     * READ
     *******************************************/
}
