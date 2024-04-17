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
     * @return mixed
     */
    public function create(array $data)
    {
        return $this->client->post($this->client->getUrl('/v1/payouts'), $data);
    }
    
    /**
     * @param string $id
     * 
     * @return array|null
     */
    public function get(string $id): ?array
    {
        return $this->client->get($this->client->getUrl("/v1/payouts/$id"));
    }
}
