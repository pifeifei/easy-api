<?php

namespace Pff\EasyApiTest\Feature;

use Pff\EasyApi\Clients\Client;
use Pff\EasyApi\Result;
use Pff\EasyApiTest\TestCase;

class ClientTest extends TestCase
{
    public function testClientSimple()
    {
        $config = $this->getConfig();
        $client = new Client($config);
        $client->mockResponse(200, [], 'testClientSimple');
        $result = $client->request();
        $this->assertInstanceOf(Result::class, $result);
        $this->assertEquals('testClientSimple', $result->getBody()->__toString());
        $this->assertEquals(200, $result->getStatusCode());
        $this->assertEquals('OK', $result->getReasonPhrase());
        $this->assertInstanceOf(\Pff\EasyApi\Request\Request::class, $result->getRequest());
        $client->forgetHistory();
        $client->cancelMock();
    }

    public function testHistory()
    {
        $config = $this->getConfig();
        $client = new Client($config);
        $client->cancelMock();

        $this->assertFalse($client->isRememberHistory());
        $client->rememberHistory();
        $this->assertTrue($client->isRememberHistory());

        $this->assertEquals(0, $client->countHistory());

        $client->mockResponse(200, [], 'first');
        $client->mockResponse(200, [], 'second');

        $client->request();
        $client->request();
        $this->assertEquals(2, $client->countHistory());

        $this->assertIsArray($histories = $client->getHistory());

        $this->assertEquals('first', $histories[0]['response']->getBody()->__toString());
        $this->assertEquals('second', $histories[1]['response']->getBody()->__toString());
        $client->forgetHistory();
    }

}
