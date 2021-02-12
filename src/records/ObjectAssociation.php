<?php

/**
 * @copyright  Copyright (c) Flipbox Digital Limited
 * @license    https://flipboxfactory.com/software/hubspot/license
 * @link       https://www.flipboxfactory.com/software/hubspot/
 */

namespace flipbox\craft\hubspot\records;

use Craft;
use flipbox\craft\hubspot\fields\ObjectsFieldInterface;
use flipbox\craft\hubspot\HubSpot;
use flipbox\craft\hubspot\migrations\ObjectAssociations;
use flipbox\craft\integration\records\EnvironmentalTableTrait;
use flipbox\craft\integration\records\IntegrationAssociation;
use Psr\Http\Message\ResponseInterface;
use yii\db\MigrationInterface;

/**
 * @author Flipbox Factory <hello@flipboxfactory.com>
 * @since 1.0.0
 *
 * @property int $fieldId
 * @property string $objectId
 */
class ObjectAssociation extends IntegrationAssociation
{
    use EnvironmentalTableTrait;

    /**
     * The table alias
     */
    const TABLE_ALIAS = 'hubspot_objects';

    /**
     * @inheritdoc
     * @throws \Throwable
     */
    public static function tableAlias()
    {
        return static::environmentTableAlias();
    }

    /**
     * @inheritdoc
     */
    protected static function environmentTableAlias(): string
    {
        return static::TABLE_ALIAS . HubSpot::getInstance()->getSettings()->environmentTableSuffix;
    }

    /**
     * @inheritdoc
     */
    protected static function createEnvironmentTableMigration():MigrationInterface
    {
        return new ObjectAssociations();
    }

    /**
     * @return ResponseInterface
     */
    public function getObject(): ResponseInterface
    {
        if (null === ($field = $this->getField())) {
            return null;
        }

        if (!$field instanceof ObjectsFieldInterface) {
            return null;
        }

        $id = $this->objectId ?: self::DEFAULT_ID;

        return $field->readFromHubSpot($id);
    }
}
