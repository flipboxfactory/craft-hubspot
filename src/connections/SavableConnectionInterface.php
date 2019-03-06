<?php

/**
 * @copyright  Copyright (c) Flipbox Digital Limited
 * @license    https://flipboxfactory.com/software/hubspot/license
 * @link       https://www.flipboxfactory.com/software/hubspot/
 */

namespace flipbox\craft\hubspot\connections;

use Flipbox\HubSpot\Connections\ConnectionInterface;

/**
 * @author Flipbox Factory <hello@flipboxfactory.com>
 * @since 2.0.0
 */
interface SavableConnectionInterface extends ConnectionInterface
{
    /**
     * Returns the display name of the connection.
     *
     * @return string
     */
    public static function displayName(): string;

    /**
     * Returns the settings html for the connection
     *
     * @return string|null
     */
    public function getSettingsHtml();

    /**
     * Validates the connection.
     *
     * @param string[]|null $attributeNames List of attribute names that should
     * be validated. If this parameter is empty, it means any attribute listed
     * in the applicable validation rules should be validated.
     * @param bool $clearErrors Whether existing errors should be cleared before
     * performing validation
     * @return bool
     */
    public function validate($attributeNames = null, $clearErrors = true);
}
