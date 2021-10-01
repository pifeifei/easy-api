<?php

namespace Pff\EasyApiTest\Unit;


use GuzzleHttp\Psr7\Response;
use GuzzleHttp\Psr7\Uri;
use Pff\EasyApi\API;
use Pff\EasyApi\Config;
use Pff\EasyApi\Request\Request;
use Pff\EasyApi\Result;
use Pff\EasyApiTest\TestCase;

class ResultTest extends TestCase
{
    /**
     * @var Result
     */
    protected $result;

    public function testResult()
    {
        $this->assertTrue($this->result->isSuccess());
        $this->assertInstanceOf(Request::class, $this->result->getRequest());
        $this->assertEquals(200, $this->result->getStatusCode());
        $this->assertEquals('{"foo":"foo","bar":true,"arr":{"a1":"v1","a2":"v2"}}', $this->result->toJson());
        $this->assertEquals('{"foo":"foo","bar":true,"arr":{"a1":"v1","a2":"v2"}}', $this->result->getBody()->__toString());
        $this->assertEquals("v1", $this->result->get('arr.a1'));
        $this->assertFalse($this->result->isEmpty());
        $this->assertTrue($this->result->has("foo"));
        $this->assertEquals(3, $this->result->count());
        $this->result->clear(['bar', 'none']);
        $this->assertEquals(2, $this->result->count());
    }

    protected function setUp(): void
    {
        $uri = new Uri('http://pifeifei.com/path/to/index.html');
        $options = [
            'proxy' => 'http://127.0.0.1:8888',
        ];
        $config = new Config([
            'config' => [
                'app_key' => 'app key string',
                'token' => 'token string'
            ],
            'request' => [
                'format' => API::RESPONSE_FORMAT_JSON
            ]
        ]);

        $response = new Response(
            200,
            [],
            '{"foo":"foo","bar":true,"arr":{"a1":"v1","a2":"v2"}}',
            '1.1'
        );
        $request = new Request('GET', $uri, $options, $config);
        $this->result = new Result($response, $request);
    }

    protected function tearDown(): void
    {
        unset($this->result);
    }
}
