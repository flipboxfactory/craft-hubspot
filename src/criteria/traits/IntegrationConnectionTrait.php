<?php

/**
 * @copyright  Copyright (c) Flipbox Digital Limited
 * @license    https://flipboxfactory.com/software/hubspot/license
 * @link       https://www.flipboxfactory.com/software/hubspot/
 */

namespace flipbox\hubspot\criteria\traits;

use flipbox\hubspot\connections\IntegrationConnectionInterface;
use flipbox\hubspot\helpers\ConnectionHelper;
use flipbox\hubspot\services\Connections;

/**
 * @author Flipbox Factory <hello@flipboxfactory.com>
 * @since 1.0.0
 */
trait IntegrationConnectionTrait
{
    /**
     * @var IntegrationConnectionInterface|string
     */
    protected $connection = Connections::DEFAULT_INTEGRATION_CONNECTION;

    /**
     * @param $value
     * @return $this
     */
    public function connection($value)
    {
        return $this->setConnection($value);
    }

    /**
     * @param $value
     * @return $this
     */
    public function setConnection($value)
    {
        $this->connection = $value;
        return $this;
    }

    /**
     * @return IntegrationConnectionInterface
     * @throws \yii\base\InvalidConfigException
     */
    public function getConnection(): IntegrationConnectionInterface
    {
        return $this->connection = ConnectionHelper::resolveIntegrationConnection($this->connection);
    }
}
