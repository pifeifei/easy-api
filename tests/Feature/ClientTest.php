<?php

declare(strict_types=1);

namespace Pff\EasyApiTest\Feature;

use Pff\EasyApi\Clients\Client;
use Pff\EasyApi\Result;
use Pff\EasyApiTest\TestCase;

/**
 * @internal
 * @coversNothing
 */
final class ClientTest extends TestCase
{
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

        static::assertIsArray($histories = $client->getHistory());

        static::assertSame('first', $histories[0]['response']->getBody()->__toString());
        static::assertSame('second', $histories[1]['response']->getBody()->__toString());
        $client->forgetHistory();
    }
}
