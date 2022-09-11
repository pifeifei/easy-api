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
 *
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
        $this->assertInstanceOf(Result::class, $result);
        $this->assertSame('testClientSimple', $result->getBody()->__toString());
        $this->assertSame(200, $result->getStatusCode());
        $this->assertSame('OK', $result->getReasonPhrase());
        $this->assertInstanceOf(\Pff\EasyApi\Request\Request::class, $result->getRequest());
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

        $this->assertFalse($client->isRememberHistory());
        $client->rememberHistory();
        $this->assertTrue($client->isRememberHistory());

        $this->assertSame(0, $client->countHistory());

        $client->mockResponse(200, [], 'first');
        $client->mockResponse(200, [], 'second');

        $client->request();
        $client->request();
        $this->assertSame(2, $client->countHistory());

        /** @var array<array{request: RequestInterface, response: Result}> $histories */
        $histories = $client->getHistory();
        $this->assertIsArray($histories);

        $this->assertSame('first', $histories[0]['response']->getBody()->__toString());
        $this->assertSame('second', $histories[1]['response']->getBody()->__toString());
        $client->forgetHistory();
    }
}
