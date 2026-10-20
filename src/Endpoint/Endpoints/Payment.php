<?php

namespace Onetoweb\Swish\Endpoint\Endpoints;

use Onetoweb\Swish\Endpoint\AbstractEndpoint;

/**
 * Payment Endpoint.
 */
class Payment extends AbstractEndpoint
{
    /**
     * @param string $instructionUuid
     * @param array $data
     * 
     * @return array|bool|null
     */
    public function create(string $instructionUuid, array $data): array|bool|null
    {
        return $this->client->put($this->client->getUrl("/v2/paymentrequests/$instructionUuid"), $data);
    }
    
    /**
     * @param string $id
     * 
     * @return array|null
     */
    public function get(string $id): array|null
    {
        return $this->client->get($this->client->getUrl("/v1/paymentrequests/$id"));
    }
    
    /**
     * @param string $id
     * 
     * @return array|null
     */
    public function cancel(string $id): array|null
    {
        return $this->client->patch($this->client->getUrl("/v1/paymentrequests/$id"), [[
            'op' => 'replace',
            'path' => '/status',
            'value' => 'cancelled'
        ]]);
    }
}
