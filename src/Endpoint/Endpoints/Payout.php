<?php

namespace Onetoweb\Swish\Endpoint\Endpoints;

use Onetoweb\Swish\Endpoint\AbstractEndpoint;

/**
 * Payout Endpoint.
 */
class Payout extends AbstractEndpoint
{
    /**
     * @param array $data
     * 
     * @return array|bool|null
     */
    public function create(array $data): array|bool|null
    {
        return $this->client->post($this->client->getUrl('/v1/payouts'), $data);
    }
    
    /**
     * @param string $id
     * 
     * @return array|null
     */
    public function get(string $id): array|null
    {
        return $this->client->get($this->client->getUrl("/v1/payouts/$id"));
    }
}
