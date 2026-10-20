<?php

namespace Onetoweb\Swish\Endpoint\Endpoints;

use Onetoweb\Swish\Endpoint\AbstractEndpoint;

/**
 * Qr Endpoint.
 */
class Qr extends AbstractEndpoint
{
    /**
     * @param array $data
     * 
     * @return array|string|null
     */
    public function commerce(array $data): array|string|null
    {
        return $this->client->postQr($this->client->getUrlQr('/v1/commerce'), $data);
    }
    
    /**
     * @param array $data
     * 
     * @return array|string|null
     */
    public function prefilled(array $data): array|string|null
    {
        return $this->client->postQr($this->client->getUrlQr('/v1/prefilled'), $data);
    }
}
