<?php

declare(strict_types=1);

namespace Pff\EasyApi\Concerns\Client;

use GuzzleHttp\HandlerStack;
use GuzzleHttp\Middleware;
use Illuminate\Support\Str;
use Pff\EasyApi\Result;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;

trait RetryTrait
{
    /**
     * @var ?callable 详情请看：\GuzzleHttp\RetryMiddleware::onFulfilled()
     */
    protected $retryDelay;

    /**
     * Server Retry Times.
     */
    private int $serverRetry = 0;

    /**
     * Server Retry Strings.
     *
     * @var string[]
     */
    private array $serverRetryStrings = [];

    /**
     * Server Retry Codes.
     *
     * @var int[]
     */
    private array $serverRetryStatusCodes = [];

    /**
     * Client Retry Times.
     */
    private int $clientRetry = 0;

    /**
     * Client Retry Strings.
     *
     * @var string[]
     */
    private array $clientRetryStrings = [];

    /**
     * Client Retry Codes.
     *
     * @var int[]
     */
    private array $clientRetryStatusCodes = [];

    /**
     * @param string[] $strings
     * @param int[] $statusCodes
     *
     * @return $this
     */
    public function retryByServer(int $times, array $strings, array $statusCodes = []): self
    {
        $this->serverRetry = $times;
        $this->serverRetryStrings = $strings;
        $this->serverRetryStatusCodes = $statusCodes;

        return $this;
    }

    public function retryDelay(callable $delay): void
    {
        $this->retryDelay = $delay;
    }

    /**
     * @param string[] $strings
     * @param int[] $codes
     *
     * @return $this
     */
    public function retryByClient(int $times, array $strings, array $codes = []): self
    {
        $this->clientRetry = $times;
        $this->clientRetryStrings = $strings;
        $this->clientRetryStatusCodes = $codes;

        return $this;
    }

    protected function pushRetryMiddleware(HandlerStack $stack): void
    {
        $stack->push($this->retryMiddleware(), 'retry');
    }

    protected function retryMiddleware(): callable
    {
        return Middleware::retry(
            function (
                $retries,
                RequestInterface $request,
                ?ResponseInterface $response = null
            ) {
                if (null === $response) {
                    return true;
                }

                $statusCode = $response->getStatusCode();
                // Limit the number of retries to 2
                if ($retries < $this->clientRetry && 400 <= $statusCode && $statusCode < 500) {
                    // Retry on server errors
                    if (\in_array($statusCode, $this->clientRetryStatusCodes, true)) {
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
                    if (\in_array($statusCode, $this->serverRetryStatusCodes, true)) {
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

                return false;
            },
            $this->retryDelay ?? null
        );
    }

    protected function isText(ResponseInterface $response): bool
    {
        $type = strtolower($response->getHeaderLine('Content-Type')); // json, xml, text, html,

        $allow = ['json', 'xml', 'text', 'html'];
        foreach ($allow as $item) {
            if (strpos($type, $item)) {
                return true;
            }
        }

        return false;
    }

    protected function shouldRetryMiddleware(): bool
    {
        if ($this->serverRetry > 0 || $this->clientRetry > 0) {
            return true;
        }

        return false;
    }

    /**
     * @deprecated 0.1.4 貌似没用了，用中间件重试请求
     *
     * @removed 1.0
     */
    private function shouldServerRetry(Result $result): bool
    {
        if ($this->serverRetry <= 0) {
            return false;
        }

        if (\in_array($result->getStatusCode(), $this->serverRetryStatusCodes, true)) {
            --$this->serverRetry;

            return true;
        }

        foreach ($this->serverRetryStrings as $message) {
            if (Str::contains($result->getBody()->getContents(), $message)) {
                --$this->serverRetry;

                return true;
            }
        }

        return false;
    }

    /**
     * @deprecated 0.1.4 貌似没用了，用中间件重试请求
     *
     * @removed 1.0
     */
    private function shouldClientRetry(\Exception $exception): bool
    {
        if ($this->clientRetry <= 0) {
            return false;
        }

        if (\in_array($exception->getCode(), $this->clientRetryStatusCodes, true)) {
            --$this->clientRetry;

            return true;
        }

        foreach ($this->clientRetryStrings as $message) {
            if (Str::contains($exception->getMessage(), $message)) {
                --$this->clientRetry;

                return true;
            }
        }

        return false;
    }
}
