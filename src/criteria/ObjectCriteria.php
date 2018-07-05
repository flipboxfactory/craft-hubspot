<?php

/**
 * @copyright  Copyright (c) Flipbox Digital Limited
 * @license    https://flipboxfactory.com/software/hubspot/license
 * @link       https://www.flipboxfactory.com/software/hubspot/
 */

namespace flipbox\hubspot\criteria;

use yii\base\BaseObject;

/**
 * @author Flipbox Factory <hello@flipboxfactory.com>
 * @since 1.0.0
 */
class ObjectCriteria extends BaseObject implements ObjectCriteriaInterface
{
    use traits\TransformerCollectionTrait,
        traits\ConnectionTrait,
        traits\CacheTrait;

    /**
     * @var string
     */
    public $id = '';

    /**
     * @inheritdoc
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @inheritdoc
     */
    public function setId(string $id = null)
    {
        $this->id = $id;
        return $this;
    }
}
