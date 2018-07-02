<?php

/**
 * @copyright  Copyright (c) Flipbox Digital Limited
 * @license    https://flipboxfactory.com/software/hubspot/license
 * @link       https://www.flipboxfactory.com/software/hubspot/
 */

namespace flipbox\hubspot\db\traits;

use craft\helpers\Db;
use yii\db\Expression;

/**
 * @author Flipbox Factory <hello@flipboxfactory.com>
 * @since 1.0.0
 */
trait ObjectIdAttribute
{
    /**
     * @var string|string[]|null
     */
    public $objectId;

    /**
     * Adds an additional WHERE condition to the existing one.
     * The new condition and the existing one will be joined using the `AND` operator.
     * @param string|array|Expression $condition the new WHERE condition. Please refer to [[where()]]
     * on how to specify this parameter.
     * @param array $params the parameters (name => value) to be bound to the query.
     * @return $this the query object itself
     * @see where()
     * @see orWhere()
     */
    abstract public function andWhere($condition, $params = []);

    /**
     * @param string|string[]|null $value
     * @return static
     */
    public function setObjectId($value)
    {
        $this->objectId = $value;
        return $this;
    }

    /**
     * @param string|string[]|null $value
     * @return static
     */
    public function objectId($value)
    {
        return $this->setObjectId($value);
    }

    /**
     * @param string|string[]|null $value
     * @return static
     */
    public function setObject($value)
    {
        return $this->setObjectId($value);
    }

    /**
     * @param string|string[]|null $value
     * @return static
     */
    public function object($value)
    {
        return $this->setObjectId($value);
    }

    /**
     *  Apply query specific conditions
     */
    protected function applyObjectIdConditions()
    {
        if ($this->objectId !== null) {
            $this->andWhere(Db::parseParam('objectId', $this->objectId));
        }
    }
}
