<?php

declare(strict_types=1);

namespace Pff\EasyApi\Concerns\Client;

use Exception;
use GuzzleHttp\Exception\RequestException;
use GuzzleHttp\Handler\MockHandler;
use GuzzleHttp\Psr7\Response;
use Illuminate\Support\Arr;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;

trait MockTrait
{
    /**
     * @var array
     */
    protected $mockQueue = [];

    /**
     * @var MockHandler
     */
    protected $mock;

    /**
     * @param array|object|string $body
     */
    public function mockResponse(int $status = 200, array $headers = [], $body = null, string $version = '1.1'): void
    {
        if (\is_array($body) || \is_object($body)) {
            $body = json_encode($body);
        }

        $this->mockQueue[] = new Response($status, $headers, $body, $version);
        $this->getMock()->append(Arr::last($this->mockQueue));
    }

    /**
     * @param string $message
     */
    public function mockRequestException(
        $message,
        RequestInterface $request,
        ResponseInterface $response = null,
        Exception $previous = null,
        array $handlerContext = []
    ): void {
        $this->mockQueue[] = new RequestException(
            $message,
            $request,
            $response,
            $previous,
            $handlerContext
        );

        $this->getMock()->append(Arr::last($this->mockQueue));
    }

    public function cancelMock(): void
    {
        $this->mockQueue = [];
        $this->getMock()->reset();
    }

    public function hasMock(): bool
    {
        return $this->getMock()->count() > 0;
    }

    public function getMock(): MockHandler
    {
        if (null === $this->mock) {
            $this->mock = new MockHandler($this->mockQueue);
        }

        return $this->mock;
    }
}
