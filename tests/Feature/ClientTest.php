<?php

declare(strict_types=1);

namespace Pff\EasyApiTest\Feature;

use Pff\EasyApi\Clients\Client;
use Pff\EasyApi\Exception\ClientException;
use Pff\EasyApi\Exception\ServerException;
use Pff\EasyApi\Result;
use Pff\EasyApiTest\TestCase;
use Psr\Http\Message\RequestInterface;

/**
 * @internal
 * @coversNothing
 */
final class ClientTest extends TestCase
{
    /**
     * @throws ClientException
     * @throws ServerException
     */
    public function testClientSimple(): void
    {
        $config = $this->getConfig();
        $client = new Client($config);
        $client->mockResponse(200, [], 'testClientSimple');
        $result = $client->request();
        static::assertInstanceOf(Result::class, $result);
        static::assertSame('testClientSimple', $result->getBody()->__toString());
        static::assertSame(200, $result->getStatusCode());
        static::assertSame('OK', $result->getReasonPhrase());
        static::assertInstanceOf(\Pff\EasyApi\Request\Request::class, $result->getRequest());
        $client->forgetHistory();
        $client->cancelMock();
    }

    /**
     * @throws ClientException
     * @throws ServerException
     */
    public function testHistory(): void
    {
        $config = $this->getConfig();
        $client = new Client($config);
        $client->cancelMock();

        static::assertFalse($client->isRememberHistory());
        $client->rememberHistory();
        static::assertTrue($client->isRememberHistory());

        static::assertSame(0, $client->countHistory());

        $client->mockResponse(200, [], 'first');
        $client->mockResponse(200, [], 'second');

        $client->request();
        $client->request();
        static::assertSame(2, $client->countHistory());

        /** @var array<array{request: RequestInterface, response: Result}> $histories */
        $histories = $client->getHistory();
        static::assertIsArray($histories);

        static::assertSame('first', $histories[0]['response']->getBody()->__toString());
        static::assertSame('second', $histories[1]['response']->getBody()->__toString());
        $client->forgetHistory();
    }
}
