<?php

declare(strict_types=1);

namespace Pff\EasyApi\Concerns\Client;

use GuzzleHttp\Exception\RequestException;
use GuzzleHttp\Handler\MockHandler;
use GuzzleHttp\Psr7\Response;
use Illuminate\Support\Arr;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\StreamInterface;

trait MockTrait
{
    /**
     * @var RequestException[]|Response[]
     */
    protected array $mockQueue = [];

    /**
     * @var ?MockHandler
     */
    protected ?MockHandler $mock = null;

    /**
     * @param array<string, string|string[]> $headers
     * @param null|resource|StreamInterface|string $body
     */
    public function mockResponse(int $status = 200, array $headers = [], $body = null, string $version = '1.1'): void
    {
        $this->mockQueue[] = new Response($status, $headers, $body, $version);
        $this->getMock()->append(Arr::last($this->mockQueue));
    }

    /**
     * @param array<string, mixed> $handlerContext
     */
    public function mockRequestException(
        string $message,
        RequestInterface $request,
        ResponseInterface $response = null,
        \Exception $previous = null,
        array $handlerContext = []
    ): void {
        $this->mockQueue[] = new RequestException($message, $request, $response, $previous, $handlerContext);

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
        if (\is_null($this->mock)) {
            $this->mock = new MockHandler($this->mockQueue);
        }

        return $this->mock;
    }
}
