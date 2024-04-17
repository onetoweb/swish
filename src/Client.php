<?php

namespace Onetoweb\Swish;

use Onetoweb\Swish\Endpoint\Endpoints;
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
     * Base Urls.
     */
    public const BASE_URL = 'https://cpc.getswish.net/swish-cpcapi';
    public const BASE_URL_QR = 'https://mpc.getswish.net/qrg-swish';
    
    /**
     * Test Base Urls.
     */
    public const BASE_URL_TEST = 'https://mss.cpc.getswish.net/swish-cpcapi';
    
    /**
     * Methods.
     */
    public const METHOD_GET = 'GET';
    public const METHOD_POST = 'POST';
    public const METHOD_PUT = 'PUT';
    public const METHOD_PATCH = 'PATCH';
    
    /**
     * @var string
     */
    private $certFile;
    
    /**
     * @var string
     */
    private $certPassword;
    
    /**
     * @var bool
     */
    private $testModus;
    
    /**
     * @var callable|null
     */
    private $handler;
    
    /**
     * @param bool $testModus = false
     * @param string $certFile = null
     * @param string $certPassword = null
     */
    public function __construct(bool $testModus = false, string $certFile = null, string $certPassword = null)
    {
        $this->testModus = $testModus;
        
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
            $baseUrl = self::BASE_URL_TEST;
        } else {
            $baseUrl = self::BASE_URL;
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
        return  self::BASE_URL_QR . "/api/".ltrim($path, '/');
    }
    
    /**
     * @param string $url
     * @param array $query = []
     * 
     * @return mixed
     */
    public function get(string $url)
    {
        return $this->request(self::METHOD_GET, $url, []);
    }
    
    /**
     * @param string $url
     * @param array $data = []
     * 
     * @return mixed
     */
    public function post(string $url, array $data = [])
    {
        return $this->request(self::METHOD_POST, $url, $data);
    }
    
    /**
     * @param string $url
     * @param array $data = []
     *
     * @return mixed
     */
    public function postQr(string $url, array $data = [])
    {
        return $this->requestQr(self::METHOD_POST, $url, $data);
    }
    
    /**
     * @param string $url
     * @param array $data = []
     * 
     * @return mixed
     */
    public function put(string $url, array $data = [])
    {
        return $this->request(self::METHOD_PUT, $url, $data);
    }
    
    /**
     * @param string $url
     * @param array $data = []
     * 
     * @return mixed
     */
    public function patch(string $url, array $data = [])
    {
        return $this->request(self::METHOD_PATCH, $url, $data);
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
     * @param string $method
     * @param string $url
     * @param array $data = []
     * 
     * @return mixed
     */
    public function requestQr(string $method, string $url, array $data = [])
    {
        $options = [
            RequestOptions::HTTP_ERRORS => false,
            RequestOptions::JSON => $data,
        ];
        
        // make request
        $response = (new GuzzleCLient())->request($method, $url, $options);
        
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
     * @param string $method
     * @param string $url
     * @param array $data = []
     * 
     * @return mixed
     */
    public function request(string $method, string $url, array $data = [])
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
                'Content-Type' => ($method === self::METHOD_PATCH ? 'application/json-patch+json' : 'application/json')
            ],
            'curl' => [
                CURLOPT_SSLVERSION => CURL_SSLVERSION_TLSv1_2,
                CURLOPT_SSLCERTTYPE => 'P12',
            ]
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
        $response = $guzzleCLient->request($method, $url, $options);
        
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
