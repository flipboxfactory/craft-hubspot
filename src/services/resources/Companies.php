<?php

/**
 * @copyright  Copyright (c) Flipbox Digital Limited
 * @license    https://flipboxfactory.com/software/hubspot/license
 * @link       https://www.flipboxfactory.com/software/hubspot/
 */

namespace flipbox\hubspot\services\resources;

use flipbox\hubspot\builders\CompanyBuilder;
use flipbox\hubspot\builders\ObjectBuilderInterface;
use flipbox\hubspot\criteria\CompanyCriteria;
use flipbox\hubspot\criteria\ObjectCriteriaInterface;
use Flipbox\Relay\HubSpot\Builder\Resources\Company\Create;
use Flipbox\Relay\HubSpot\Builder\Resources\Company\Delete;
use Flipbox\Relay\HubSpot\Builder\Resources\Company\Read;
use Flipbox\Relay\HubSpot\Builder\Resources\Company\Update;
use yii\base\Component;

/**
 * @author Flipbox Factory <hello@flipboxfactory.com>
 * @since 1.0.0
 */
class Companies extends Component implements CRUDInterface
{
    use traits\SyncByElementTrait,
        traits\ReadObjectTrait,
        traits\UpsertObjectTrait,
        traits\DeleteObjectTrait;

    /**
     * The HubSpot Resource name
     */
    const HUBSPOT_RESOURCE = 'companies';

    /**
     * @param array $config
     * @return ObjectCriteriaInterface
     */
    public function getCriteria(array $config = []): ObjectCriteriaInterface
    {
        return new CompanyCriteria($config);
    }

    /**
     * @param array $config
     * @return ObjectBuilderInterface
     */
    public function getBuilder(array $config = []): ObjectBuilderInterface
    {
        return new CompanyBuilder($config);
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
        return Read::class;
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
}
