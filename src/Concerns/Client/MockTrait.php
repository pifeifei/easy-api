<?php

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
     * @param integer $status
     * @param array $headers
     * @param array|string|object $body
     * @param string $version
     */
    public function mockResponse(int $status = 200, array $headers = [], $body = null, string $version = '1.1')
    {
        if (is_array($body) || is_object($body)) {
            $body = json_encode($body);
        }

        $this->mockQueue[] = new Response($status, $headers, $body, $version);
        $this->getMock()->append(Arr::last($this->mockQueue));
    }

    /**
     * @param string                 $message
     * @param RequestInterface       $request
     * @param ResponseInterface|null $response
     * @param Exception|null         $previous
     * @param array                  $handlerContext
     */
    public function mockRequestException(
        $message,
        RequestInterface $request,
        ResponseInterface $response = null,
        Exception $previous = null,
        array $handlerContext = []
    ) {
        $this->mockQueue[] = new RequestException(
            $message,
            $request,
            $response,
            $previous,
            $handlerContext
        );

        $this->getMock()->append(Arr::last($this->mockQueue));
    }

    public function cancelMock()
    {
        $this->mockQueue = [];
        $this->getMock()->reset();
    }

    /**
     * @return bool
     */
    public function hasMock(): bool
    {
        return $this->getMock()->count() > 0;
    }

    /**
     * @return MockHandler
     */
    public function getMock(): MockHandler
    {
        if (is_null($this->mock)) {
            $this->mock = new MockHandler($this->mockQueue);
        }
        return $this->mock;
    }
}
