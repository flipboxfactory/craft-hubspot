<?php

/**
 * @copyright  Copyright (c) Flipbox Digital Limited
 * @license    https://flipboxfactory.com/software/hubspot/license
 * @link       https://www.flipboxfactory.com/software/hubspot/
 */

namespace flipbox\hubspot\connections;

use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;

/**
 * @author Flipbox Factory <hello@flipboxfactory.com>
 * @since 1.0.0
 */
class ApplicationKeyConnection implements ConnectionInterface
{
    /**
     * @var
     */
    public $key;

    /**
     * @var
     */
    public $hubId;

    /**
     * @return string
     */
    public function getHubId(): string
    {
        return $this->hubId;
    }

    /**
     * Add the 'hapikey' to the query
     *
     * @inheritdoc
     */
    public function prepareAuthorizationRequest(
        RequestInterface $request
    ): RequestInterface {

        // Requested URI
        $uri = $request->getUri();

        // Get Query
        $query = $uri->getQuery();

        // Append to?
        if (!empty($query)) {
            $query .= '&';
        }

        // Add our key
        $query .= http_build_query([
            'hapikey' => $this->key
        ]);

        return $request->withUri(
            $uri->withQuery($query)
        );
    }

    /**
     * We can't do much, just return the response
     *
     * @inheritdoc
     */
    public function handleAuthorizationResponse(
        ResponseInterface $response,
        RequestInterface $request,
        callable $callable
    ): ResponseInterface {
        return $response;
    }
}
