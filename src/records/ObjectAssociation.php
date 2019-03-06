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
use flipbox\craft\integration\records\IntegrationAssociation;
use Psr\Http\Message\ResponseInterface;

/**
 * @author Flipbox Factory <hello@flipboxfactory.com>
 * @since 1.0.0
 *
 * @property int $fieldId
 * @property string $objectId
 */
class ObjectAssociation extends IntegrationAssociation
{
    /**
     * The table alias
     */
    const TABLE_ALIAS = 'hubspot_objects';

    /**
     * @inheritdoc
     * @throws \Throwable
     */
    public function __construct(array $config = [])
    {
        $this->ensureTableExists();
        parent::__construct($config);
    }


    /**
     * @throws \Throwable
     */
    public function ensureTableExists()
    {
        if (!in_array(
            Craft::$app->getDb()->tablePrefix . static::tableAlias(),
            Craft::$app->getDb()->getSchema()->tableNames,
            true
        )) {
            $this->createTable();
        }
    }

    /**
     * @return bool
     * @throws \Throwable
     */
    private function createTable(): bool
    {
        ob_start();
        (new ObjectAssociations())->up();
        ob_end_clean();

        return true;
    }

    /**
     * {@inheritdoc}
     */
    public static function tableAlias()
    {
        return parent::tableAlias() . HubSpot::getInstance()->getSettings()->environmentTablePostfix;
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
