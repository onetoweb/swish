<?php

namespace Onetoweb\Swish\Endpoint;

use Onetoweb\Swish\Client;

/**
 * Abstract Endpoint.
 */
abstract class AbstractEndpoint implements EndpointInterface
{
    /**
     * @param Client $client
     */
    public function __construct(
        protected Client $client
    ) {
        
    }
}
