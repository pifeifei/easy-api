<?php

declare(strict_types=1);

namespace Pff\EasyApiTest\Feature;

use Pff\EasyApi\Clients\Client;
use Pff\EasyApi\Exception\ClientException;
use Pff\EasyApi\Exception\ServerException;
use Pff\EasyApiTest\TestCase;

/**
 * @internal
 * @coversNothing
 */
final class RetryTest extends TestCase
{
    public function testNoRetry(): void
    {
        try {
            $config = $this->getConfig();
            $client = new Client($config);
            $client->cancelMock();

            $client->mockResponse(400, [], '{"code":400}');

            $client->request();
        } catch (ServerException|ClientException $e) {
            static::assertGreaterThan(0, $e->getCode());
        }
    }

    public function testRetryWithStrings(): void
    {
        $config = $this->getConfig();
        $client = new Client($config);
        $client->cancelMock();

        $headers = ['Content-Type' => 'application/json'];
        $client->mockResponse(400, $headers, '{"code":400,"message":"error: client error"}');
        $client->mockResponse(401, $headers, '{"code":401,"message":"error: file not find."}');
        $client->mockResponse(200, $headers, '{"code":200}');

        $client->retryByClient(10, ['client error', 'not find']);
        $response = $client->request();

        static::assertSame(['code' => 200], $response->all());
        static::assertSame(200, $response->getStatusCode());
    }

    public function testRetryWithStatusCode(): void
    {
        $config = $this->getConfig();
        $client = new Client($config);
        $client->cancelMock();

        $client->mockResponse(400, [], '{"code":400}');
        $client->mockResponse(401, [], '{"code":401}');
        $client->mockResponse(200, [], '{"code":200}');

        $client->retryByClient(10, [], [400, 401]);
        $response = $client->request();
        static::assertSame(['code' => 200], $response->all());
        static::assertSame(200, $response->getStatusCode());
    }
}
