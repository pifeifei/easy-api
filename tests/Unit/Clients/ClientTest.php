<?php

declare(strict_types=1);

namespace Pff\EasyApiTest\Unit\Clients;

use Pff\EasyApi\API;
use Pff\EasyApi\Clients\Client;
use Pff\EasyApi\Exception\ClientException;
use Pff\EasyApi\Exception\ServerException;
use Pff\EasyApiTest\TestCase;

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
    public function testClient(): void
    {
        $config = $this->getConfig();
        $client = new Client($config);
        $client->mockResponse();
        static::assertTrue(true);
        $response = $client->request();
        static::assertEmpty($response->getBody()->__toString());
        static::assertSame(200, $response->getStatusCode());

        $client->cancelMock();
    }

    /**
     * @throws ClientException
     */
    public function testUriTrait(): void
    {
        $config = $this->getConfig();
        $client = new Client($config);

        $client->scheme('http');
        static::assertSame('http', $client->uri()->getScheme());

        $client->host('pff.sample.cn');
        static::assertSame('pff.sample.cn', $client->uri()->getHost());

        $client->path('path/to');
        static::assertSame('/anything/path/to', $client->uri()->getPath());

        $client->path('/path/to');
        static::assertSame('/path/to', $client->uri()->getPath());

        $client->path('../path/to');
        static::assertSame('/anything/../path/to', $client->uri()->getPath());

        $client->setMethod(API::RESPONSE_FORMAT_JSON);
        static::assertSame(API::RESPONSE_FORMAT_JSON, $client->getMethod());

        $client->port(99);
        static::assertSame(99, $client->uri()->getPort());
    }
}
