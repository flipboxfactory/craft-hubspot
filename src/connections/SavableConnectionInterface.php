<?php

/**
 * @copyright  Copyright (c) Flipbox Digital Limited
 * @license    https://flipboxfactory.com/software/hubspot/license
 * @link       https://www.flipboxfactory.com/software/hubspot/
 */

namespace flipbox\craft\hubspot\connections;

use Flipbox\HubSpot\Connections\ConnectionInterface;
use flipbox\craft\integration\connections\SavableConnectionInterface as BaseSavableConnectionInterface;

interface SavableConnectionInterface extends ConnectionInterface, BaseSavableConnectionInterface
{
}
