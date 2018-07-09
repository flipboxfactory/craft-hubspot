<?php

/**
 * @copyright  Copyright (c) Flipbox Digital Limited
 * @license    https://flipboxfactory.com/software/hubspot/license
 * @link       https://www.flipboxfactory.com/software/hubspot/
 */

namespace flipbox\hubspot\criteria;

use flipbox\ember\helpers\ObjectHelper;
use flipbox\hubspot\HubSpot;
use yii\base\BaseObject;

/**
 * @author Flipbox Factory <hello@flipboxfactory.com>
 * @since 1.0.0
 */
class TimelineEventAccessor extends BaseObject implements TimelineEventAccessorInterface
{
    use traits\TransformerCollectionTrait,
        traits\IntegrationConnectionTrait,
        traits\CacheTrait;

    /**
     * The event Id
     *
     * @var string
     */
    public $id;

    /**
     * The event type Id
     *
     * @var string
     */
    public $typeId;

    /**
     * @return string
     */
    public function getTypeId(): string
    {
        return $this->typeId;
    }

    /**
     * @return string
     */
    public function getId(): string
    {
        return (string)$this->id;
    }

    /**
     * @param array $config
     * @param null $source
     * @return mixed
     * @throws \yii\base\InvalidConfigException
     */
    public function read(array $config = [], $source = null)
    {
        $this->prepare($config);
        return HubSpot::getInstance()->getResources()->getTimelineEvents()->read($this, $source);
    }

    /**
     * @inheritdoc
     */
    protected function prepare(array $criteria = [])
    {
        ObjectHelper::populate(
            $this,
            $criteria
        );
    }
}
