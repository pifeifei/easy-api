<?php

namespace Pff\EasyApiTest\Feature;

use Pff\EasyApi\Clients\Client;
use Pff\EasyApi\Exception\ServerException;
use Pff\EasyApiTest\TestCase;

class RetryTest extends TestCase
{
    public function testNoRetry()
    {
        $config = $this->getConfig();
        $client = new Client($config);
        $client->cancelMock();

        $client->mockResponse(400, [], '{"code":400}');

        try {
            $client->request();
        } catch (ServerException $e) {
            $this->assertGreaterThan(0, $e->getCode());
        }
    }

    public function testRetryWithStrings()
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

        $this->assertEquals(['code' => 200], $response->all());
        $this->assertEquals(200, $response->getStatusCode());
    }

    public function testRetryWithStatusCode()
    {
        $config = $this->getConfig();
        $client = new Client($config);
        $client->cancelMock();

        $client->mockResponse(400, [], '{"code":400}');
        $client->mockResponse(401, [], '{"code":401}');
        $client->mockResponse(200, [], '{"code":200}');

        $client->retryByClient(10, [], [400, 401]);
        $response = $client->request();
        $this->assertEquals(['code' => 200], $response->all());
        $this->assertEquals(200, $response->getStatusCode());
    }
}
