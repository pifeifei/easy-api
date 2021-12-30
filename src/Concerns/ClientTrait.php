<?php

namespace Pff\EasyApi\Concerns;

use GuzzleHttp\Client as GuzzleClient;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\Middleware;
use Illuminate\Support\Arr;
use Pff\EasyApi\API;
use Pff\EasyApi\Clients\Client;
use Pff\EasyApi\Contracts\SignatureInterface;
use Pff\EasyApi\Request\Request;
use Pff\EasyApi\Result;
use Psr\Http\Message\ResponseInterface;

trait ClientTrait
{
    protected $methods = [
        API::METHOD_GET => 'GET',
        API::METHOD_JSON => 'POST',
        API::METHOD_POST => 'POST',
        API::METHOD_XML => 'POST',
    ];
    /**
     * @var string
     */
    protected $method;

    /**
     * @var SignatureInterface
     */
    protected $signature;

    /**
     * @var bool
     */
    protected $isTokenClient = false;

    /**
     * @param Client|null $client
     *
     * @return GuzzleClient
     */
    public function createClient(Client $client = null): GuzzleClient
    {
        if (self::hasMock()) {
//            echo __METHOD__ . ':' . __LINE__ . PHP_EOL;
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

//        if (API::getLogger()) {
//            $stack->push(Middleware::log(
//                API::getLogger(),
//                new MessageFormatter(API::getLogFormat())
//            ));
//        }

        $request = new Request($client->method(), $client->uri(), $client->options, $this->config());

        $stack->push(Middleware::mapResponse(static function (ResponseInterface $response) use ($request) {
            return new Result($response, $request);
        }));

        $this->options(['handler' => $stack]);

        return new GuzzleClient($this->options);
    }

    /**
     * setter or getter method
     * @param string|null $method
     * @return $this|string
     */
    public function method(string $method = null)
    {
        if (is_null($method)) {
            return $this->method;
        }

        $this->method = strtoupper($method);
        return $this;
    }

    /**
     * @return string
     */
    public function requestMethod()
    {
        return Arr::get($this->methods, $this->method(), 'POST');
    }

    /**
     * @return SignatureInterface
     */
    public function getSignature()
    {
        return $this->signature;
    }

    /**
     *
     * @param SignatureInterface|string|null $signature
     * @return $this
     */
    public function setSignature($signature = null)
    {
        if (is_null($signature)) {
            return $this;
        }

        if (is_string($signature) && class_exists($signature)) {
            $this->signature = new $signature;
            return $this;
        }

        if ($signature instanceof SignatureInterface) {
            $this->signature = $signature;
            return $this;
        }

//        throw new ClientException();
        throw new \UnexpectedValueException(sprintf('%s class does not exist.', $signature));
    }

    /**
     * @param bool|null $isTokenClient
     * @return $this|bool
     */
    public function tokenClient(bool $isTokenClient = null)
    {
        if (is_null($isTokenClient)) {
            return $this->isTokenClient;
        }

        $this->isTokenClient = (bool) $isTokenClient;
        return $this;
    }
}
