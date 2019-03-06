<?php

/**
 * @copyright  Copyright (c) Flipbox Digital Limited
 * @license    https://flipboxfactory.com/software/hubspot/license
 * @link       https://www.flipboxfactory.com/software/hubspot/
 */

namespace flipbox\craft\hubspot\fields;

use Craft;
use craft\helpers\Json;
use flipbox\craft\hubspot\criteria\ContactListCriteria;
use Psr\Http\Message\ResponseInterface;

/**
 * @author Flipbox Factory <hello@flipboxfactory.com>
 * @since 1.0.0
 */
class ContactLists extends Objects
{
    /**
     * @inheritdoc
     */
    public function getObjectLabel(): string
    {
        return 'Contact List';
    }

    /**
     * @inheritdoc
     */
    public static function displayName(): string
    {
        return Craft::t('hubspot', 'HubSpot: Contact Lists');
    }

    /**
     * @inheritdoc
     */
    public static function defaultSelectionLabel(): string
    {
        return Craft::t('hubspot', 'Add a HubSpot Contact List');
    }

    /**
     * @inheritdoc
     * @throws \flipbox\craft\integration\exceptions\ConnectionNotFound
     */
    protected function upsertToHubSpot(
        array $payload,
        string $id = null
    ): ResponseInterface {
        return (new ContactListCriteria([
            'connection' => $this->getConnection(),
            'cache' => $this->getCache(),
            'payload' => $payload,
            'id' => $id
        ]))->upsert();
    }

    /**
     * @inheritdoc
     * @throws \flipbox\craft\integration\exceptions\ConnectionNotFound
     * @throws \Exception
     */
    public function readFromHubSpot(
        string $id
    ): ResponseInterface {
        return (new ContactListCriteria([
            'connection' => $this->getConnection(),
            'cache' => $this->getCache(),
            'id' => $id
        ]))->read();
    }

    /**
     * @param ResponseInterface $response
     * @return string|null
     */
    protected function getObjectIdFromResponse(ResponseInterface $response)
    {
        $data = Json::decodeIfJson(
            $response->getBody()->getContents()
        );

        $id = $data['listId'] ?? null;

        return $id ? (string)$id : null;
    }
}
