<?php

namespace Onetoweb\Swish;

use Onetoweb\Swish\Endpoint\Endpoints;

use Onetoweb\Swish\Config\{Method, BaseUrl};
use GuzzleHttp\RequestOptions;
use GuzzleHttp\Client as GuzzleCLient;
use GuzzleHttp\HandlerStack;

/**
 * Swish Api Client.
 */
#[\AllowDynamicProperties]
class Client
{
    /**
     * @var callable|null
     */
    private $handler = null;
    
    /**
     * @param bool $testModus = false
     * @param string $certFile = null
     * @param string $certPassword = null
     */
    public function __construct(
        
        private bool $testModus = false,
        
        #[\SensitiveParameter]
        private ?string $certFile = null,
        
        #[\SensitiveParameter]
        private ?string $certPassword = null
    ) {
       
        if ($this->testModus and $certFile === null) {
            
            $this->certFile = __DIR__ . '/../cert/Swish_Merchant_TestCertificate_1234679304.p12';
            $this->certPassword = 'swish';
            
        } else {
            
            $this->certFile = $certFile;
            $this->certPassword = $certPassword;
        }
        
        // load endpoints
        $this->loadEndpoints();
    }
    
    /**
     * @return void
     */
    private function loadEndpoints(): void
    {
        foreach (Endpoints::list() as $name => $class) {
            $this->{$name} = new $class($this);
        }
    }
    
    /**
     * @param string $path
     * 
     * @return string
     */
    public function getUrl(string $path): string
    {
        if ($this->testModus) {
            $baseUrl = BaseUrl::TEST->value;
        } else {
            $baseUrl = BaseUrl::LIVE->value;
        }
        
        return "$baseUrl/api/".ltrim($path, '/');
    }
    
    /**
     * @param string $path
     * 
     * @return string
     */
    public function getUrlQr(string $path): string
    {
        return  BaseUrl::QR->value.'/api/'.ltrim($path, '/');
    }
    
    /**
     * @param string $url
     * @param array $query = []
     * 
     * @return array|bool|string|null
     */
    public function get(string $url): array|bool|string|null
    {
        return $this->request(Method::GET, $url, []);
    }
    
    /**
     * @param string $url
     * @param array $data = []
     * 
     * @return array|bool|string|null
     */
    public function post(string $url, array $data = []): array|bool|string|null
    {
        return $this->request(Method::POST, $url, $data);
    }
    
    /**
     * @param string $url
     * @param array $data = []
     *
     * @return array|bool|string|null
     */
    public function postQr(string $url, array $data = []): array|bool|string|null
    {
        return $this->requestQr(Method::POST, $url, $data);
    }
    
    /**
     * @param string $url
     * @param array $data = []
     * 
     * @return array|bool|string|null
     */
    public function put(string $url, array $data = []): array|bool|string|null
    {
        return $this->request(Method::PUT, $url, $data);
    }
    
    /**
     * @param string $url
     * @param array $data = []
     * 
     * @return array|bool|string|null
     */
    public function patch(string $url, array $data = []): array|bool|string|null
    {
        return $this->request(Method::PATCH, $url, $data);
    }
    
    /**
     * @param callable $handler
     * 
     * @return void
     */
    public function setHandler(callable $handler): void
    {
        $this->handler = $handler;
    }
    
    /**
     * @param Method $method
     * @param string $url
     * @param array $data = []
     * 
     * @return array|string|null
     */
    public function requestQr(Method $method, string $url, array $data = []): array|string|null
    {
        $options = [
            RequestOptions::HTTP_ERRORS => false,
            RequestOptions::JSON => $data,
        ];
        
        // make request
        $response = (new GuzzleCLient())->request($method->value, $url, $options);
        
        // get contents
        $contents = $response->getBody()->getContents();
        
        if ($response->getHeaderLine('Content-Type') === 'application/json') {
            
            // return json response
            return json_decode($contents, true);
            
        } else {
            
            // return raw response
            return $contents;
        }
    }
    
    /**
     * @param Method $method
     * @param string $url
     * @param array $data = []
     * 
     * @return array|bool|string|null
     */
    public function request(Method $method, string $url, array $data = []): array|bool|string|null
    {
        $options = [
            RequestOptions::HTTP_ERRORS => false,
            RequestOptions::CERT => [
                $this->certFile,
                $this->certPassword
            ],
            RequestOptions::VERIFY => __DIR__ . '/../cert/Swish_TLS_RootCA.pem',
            RequestOptions::JSON => $data,
            RequestOptions::HEADERS => [
                'Content-Type' => ($method === Method::PATCH ? 'application/json-patch+json' : 'application/json')
            ],
            RequestOptions::CRYPTO_METHOD => STREAM_CRYPTO_METHOD_TLSv1_2_CLIENT,
            RequestOptions::CERT_TYPE => 'p12',
        ];
        
        // init client
        if ($this->handler !== null) {
            
            // init with custom handler
            $stack = new HandlerStack();
            $stack->setHandler($this->handler);
            
            $guzzleCLient = new GuzzleCLient(['handler' => $stack]);
            
        } else {
            $guzzleCLient = new GuzzleCLient();
        }
        
        // make request
        $response = $guzzleCLient->request($method->value, $url, $options);
        
        // get contents
        $contents = $response->getBody()->getContents();
        
        if ($response->getHeaderLine('Content-Type') === 'application/json') {
            
            // return json response
            return json_decode($contents, true);
            
        } elseif($contents === '') {
            
            // return bool on success
            return $response->getStatusCode() >= 200 and $response->getStatusCode() < 300;
            
        } else {
            
            // return raw response
            return $contents;
        }
    }
}
