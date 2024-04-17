<?php

namespace Onetoweb\Swish\Handler;

use Psr\Http\Message\RequestInterface;
use Symfony\Component\Process\Process;
use GuzzleHttp\Promise\{PromiseInterface, FulfilledPromise};
use GuzzleHttp\Exception\RequestException;
use GuzzleHttp\Promise\Create;
use GuzzleHttp\Psr7\Response;
use Exception;

/**
 * Curl System Handler.
 * 
 * Run curl on the system.
 */
class CurlSystemHandler
{
    /**
     * @var string
     */
    private $curlBin;
    
    /**
     * @param string $curlBin
     */
    public function __construct(string $curlBin)
    {
        $this->curlBin = $curlBin;
    }
    
    /**
     * @param RequestInterface $request
     * @return PromiseInterface
     */
    public function __invoke(RequestInterface $request, array $options = []): PromiseInterface
    {
        // tmp header file
        $tmpHeaderFile = tempnam(sys_get_temp_dir(), 'headers');
        
        // build command
        $command = [
            $this->curlBin,
            '--request',
            $request->getMethod(),
            $request->getUri(),
            '--dump-header',
            $tmpHeaderFile,
            '--cert',
            implode(':', $options['cert']),
            '--cert-type',
            'p12',
            '--cacert',
            $options['verify'],
            '--tlsv1.2'
        ];
        
        // add headers
        foreach ($request->getHeaders() as $headerName => $headerValues) {
            
            $command[] = '--header';
            $command[] = $headerName.': '.implode(', ', $headerValues);
        }
        
        // add body
        if ($request->getBody()) {
            
            $command[] = '--data';
            $command[] = $request->getBody();
        }
        
        // process command
        $process = new Process($command);
        
        try {
            
            $process->mustRun();
            
        } catch (Exception $exception) {
            
            return Create::rejectionFor(
                new RequestException(
                    'Bad request',
                    $request,
                    null,
                    $exception
                )
            );
        }
        
        // get contents
        $contents = $process->getOutput();
        
        // get header data
        $headerData = file_get_contents($tmpHeaderFile);
        
        $statusCode = 500;
        $httpVersion = '1.1';
        $headers = [];
        foreach (explode(PHP_EOL, $headerData) as $key => $headerDataLine) {
            
            $headerDataLine = rtrim($headerDataLine);
            
            if ($headerDataLine) {
                
                if ($key === 0) {
                    
                    list(
                        $http,
                        $statusCode
                    ) = explode(' ', $headerDataLine);
                    
                    $httpVersion = str_replace('HTTP/', '', $http);
                    
                } else {
                    
                    list(
                        $headerKey,
                        $headerValue
                    ) = explode(': ', $headerDataLine, 2);
                    
                    $headers[$headerKey] = $headerValue;
                }
            }
        }
        
        // build response
        $response = new Response((int) $statusCode, $headers, $contents, $httpVersion);
        
        return new FulfilledPromise($response);
    }
}
