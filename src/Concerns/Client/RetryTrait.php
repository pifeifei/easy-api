<?php

namespace Pff\EasyApi\Concerns\Client;

use Exception;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\Middleware;
use Illuminate\Support\Str;
use Pff\EasyApi\Exception\ClientException;
use Pff\EasyApi\Result;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;

trait RetryTrait
{
    /**
     * Server Retry Times
     *
     * @var int
     */
    private $serverRetry = 0;

    /**
     * Server Retry Strings
     *
     * @var string[]
     */
    private $serverRetryStrings = [];

    /**
     * Server Retry Codes
     *
     * @var int[]
     */
    private $serverRetryStatusCodes = [];

    /**
     * Client Retry Times
     *
     * @var int
     */
    private $clientRetry = 0;

    /**
     * Client Retry Strings
     *
     * @var string[]
     */
    private $clientRetryStrings = [];

    /**
     * Client Retry Codes
     *
     * @var int[]
     */
    private $clientRetryStatusCodes = [];

    /**
     * @param int   $times
     * @param array $strings
     * @param array $statusCodes
     *
     * @return $this
     * @throws ClientException
     */
    public function retryByServer($times, array $strings, array $statusCodes = [])
    {
        $this->serverRetry            = (int)$times;
        $this->serverRetryStrings     = $strings;
        $this->serverRetryStatusCodes = $statusCodes;

        return $this;
    }

    /**
     * @param int   $times
     * @param array $strings
     * @param array $codes
     *
     * @return $this
     */
    public function retryByClient($times, array $strings, array $codes = [])
    {
        $this->clientRetry            = (int) $times;
        $this->clientRetryStrings     = $strings;
        $this->clientRetryStatusCodes = $codes;

        return $this;
    }

    protected function pushRetryMiddleware(HandlerStack $stack)
    {
        $stack->push($this->retryMiddleware(), 'retry');
    }

    /**
     * @return callable
     */
    protected function retryMiddleware()
    {
        return Middleware::retry(
            function (
                $retries,
                RequestInterface $request,
                ResponseInterface $response = null
            ){
                $statusCode = $response->getStatusCode();
                // Limit the number of retries to 2
                if ($retries < $this->clientRetry && 400 <= $statusCode && $statusCode < 500) {
                    // Retry on server errors
                    if (in_array($statusCode, $this->clientRetryStatusCodes)) {
                        return true;
                    }

                    // response 判断是文本格式，避免大文件占用内存：图片，文件
                    if ($this->isText($response)) {
                        $body = $response->getBody()->__toString();
                        foreach ($this->clientRetryStrings as $message) {
                            if (false !== strpos($body, $message)) {
                                return true;
                            }
                        }
                    }
                }

                if ($retries < $this->serverRetry && 500 <= $statusCode && $statusCode < 600) {
                    // Retry on server errors
                    if (in_array($statusCode, $this->serverRetryStatusCodes)) {
                        return true;
                    }

                    if ($this->isText($response)) {
                        $body = $response->getBody()->__toString();
                        foreach ($this->serverRetryStrings as $message) {
                            if (false !== strpos($body, $message)) {
                                return true;
                            }
                        }
                    }
                }
//                echo ("retry  {$statusCode}  {$this->clientRetry}  : " . __FILE__ . ':' . __LINE__) . PHP_EOL;
                return false;
            }, function () {
            return 500;
        });
    }

    /**
     * @param ResponseInterface $response
     * @return  bool
     */
    protected function isText(ResponseInterface $response)
    {
        $type = strtolower($response->getHeaderLine('Content-Type'));// json, xml, text, html,

        $allow = ['json', 'xml', 'text', 'html'];
        foreach ($allow as $item) {
            if (strpos($type, $item)) {
                return true;
            }
        }
        return false;
    }

    /**
     * @return bool
     */
    protected function shouldRetryMiddleware()
    {
        if ($this->serverRetry > 0 || $this->clientRetry > 0) {
            return true;
        }

        return false;
    }
    /**
     * @param Result $result
     *
     * @return bool
     */
    private function shouldServerRetry(Result $result)
    {
        if ($this->serverRetry <= 0) {
            return false;
        }

        if (in_array($result->getStatusCode(), $this->serverRetryStatusCodes)) {
            $this->serverRetry--;

            return true;
        }

        foreach ($this->serverRetryStrings as $message) {
            if (Str::contains($result->getBody(), $message)) {
                $this->serverRetry--;

                return true;
            }
        }

        return false;
    }

    /**
     * @param Exception $exception
     *
     * @return bool
     */
    private function shouldClientRetry(Exception $exception)
    {
        if ($this->clientRetry <= 0) {
            return false;
        }

        if (in_array($exception->getCode(), $this->clientRetryStatusCodes, true)) {
            $this->clientRetry--;

            return true;
        }

        foreach ($this->clientRetryStrings as $message) {
            if (Str::contains($exception->getMessage(), $message)) {
                $this->clientRetry--;

                return true;
            }
        }

        return false;
    }
}
