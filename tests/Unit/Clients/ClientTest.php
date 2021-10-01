<?php

namespace Clients;

use Pff\EasyApi\API;
use Pff\EasyApi\Clients\Client;
use Pff\EasyApiTest\TestCase;

class ClientTest extends TestCase
{

    public function testClient()
    {
        $config = $this->getConfig();
        $client = new Client($config);
        $client->mockResponse();
        $this->assertTrue(true);
        $response = $client->request();
        $this->assertEmpty($response->getBody()->__toString());
        $this->assertEquals(200, $response->getStatusCode());

        $client->cancelMock();
    }

    public function testUriTrait()
    {
        $config = $this->getConfig();
        $client = new Client($config);

        $client->scheme('http');
        $this->assertEquals('http', $client->uri()->getScheme());

        $client->host('pff.sample.cn');
        $this->assertEquals('pff.sample.cn', $client->uri()->getHost());

        $client->path('path/to');
        $this->assertEquals('/anything/path/to', $client->uri()->getPath());

        $client->path('/path/to');
        $this->assertEquals('/path/to', $client->uri()->getPath());

        $client->path('../path/to');
        $this->assertEquals('/anything/../path/to', $client->uri()->getPath());

        $client->method(API::RESPONSE_FORMAT_JSON);
        $this->assertEquals(API::RESPONSE_FORMAT_JSON, $client->method());

        $client->port(99);
        $this->assertEquals(99, $client->uri()->getPort());
    }
}
