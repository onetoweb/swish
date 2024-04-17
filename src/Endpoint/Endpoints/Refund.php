<?php

namespace Onetoweb\Swish\Endpoint\Endpoints;

use Onetoweb\Swish\Endpoint\AbstractEndpoint;

/**
 * Refund Endpoint.
 */
class Refund extends AbstractEndpoint
{
    /**
     * @param string $instructionUuid
     * @param array $data
     * 
     * @return mixed
     */
    public function create(string $instructionUuid, array $data)
    {
        return $this->client->put($this->client->getUrl("/v2/refunds/$instructionUuid"), $data);
    }
    
    /**
     * @param string $instructionUuid
     * 
     * @return array|null
     */
    public function get(string $instructionUuid)
    {
        return $this->client->get($this->client->getUrl("/v1/refunds/$instructionUuid"));
    }
}
