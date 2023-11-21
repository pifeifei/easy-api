<?php

declare(strict_types=1);

namespace Pff\EasyApi\Concerns;

use GuzzleHttp\Client as GuzzleClient;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\Middleware;
use Illuminate\Support\Arr;
use Pff\EasyApi\API;
use Pff\EasyApi\Clients\Client;
use Pff\EasyApi\Contracts\SignatureInterface;
use Pff\EasyApi\Exception\InvalidArgumentException;
use Pff\EasyApi\Request\Request;
use Pff\EasyApi\Result;
use Psr\Http\Message\ResponseInterface;

trait ClientTrait
{
    /** @var array|string[] */
    protected array $methods = [
        API::METHOD_GET => 'GET',
        API::METHOD_JSON => 'POST',
        API::METHOD_POST => 'POST',
        API::METHOD_XML => 'POST',
    ];

    protected string $method;

    protected SignatureInterface $signature;

    protected bool $isTokenClient = false;

    public function createClient(Client $client = null): GuzzleClient
    {
        if (self::hasMock()) {
            $stack = HandlerStack::create(self::getMock());
        } else {
            $stack = HandlerStack::create();
        }

        if ($this->isRememberHistory()) {
            $stack->push(Middleware::history($this->histories));
        }

        if ($this->shouldRetryMiddleware()) {
            $this->pushRetryMiddleware($stack);
        }

        /** @phpstan-ignore-next-line to do: dumped type 正常，就是会报错 */
        $request = new Request($client->getMethod(), $client->uri(), $client->getOptions(), $this->config());

        $stack->push(Middleware::mapResponse(static function (ResponseInterface $response) use ($request) {
            return new Result($response, $request);
        }));

        return new GuzzleClient(array_merge($this->getOptions(), ['handler' => $stack]));
    }

    public function getMethod(): string
    {
        return $this->method;
    }

    public function setMethod(string $method): self
    {
        $this->method = $method;

        return $this;
    }

    /**
     * setter or getter method.
     *
     * @return $this|string
     *
     * @deprecated 0.1.4 use getMethod() and setMethod()
     *
     * @removed 1.0
     */
    public function method(string $method = null)
    {
        if (null === $method) {
            return $this->method;
        }

        $this->method = strtoupper($method);

        return $this;
    }

    public function requestMethod(): string
    {
        /** @var string */
        return Arr::get($this->methods, $this->getMethod(), 'POST');
    }

    public function getSignature(): SignatureInterface
    {
        return $this->signature;
    }

    /**
     * @param null|class-string|SignatureInterface $signature
     *
     * @return $this
     */
    public function setSignature($signature = null): self
    {
        if (null === $signature) {
            return $this;
        }

        if (\is_string($signature) && class_exists($signature) && ($obj = new $signature()) instanceof SignatureInterface) {
            $this->signature = $obj;

            return $this;
        }

        if ($signature instanceof SignatureInterface) {
            $this->signature = $signature;

            return $this;
        }

        throw new InvalidArgumentException(sprintf('%s class does not exist.', $signature));
    }

    public function isTokenClient(): bool
    {
        return $this->isTokenClient;
    }

    /**
     * @return $this
     */
    public function tokenClient(bool $isTokenClient = true): self
    {
        $this->isTokenClient = $isTokenClient;

        return $this;
    }
}
