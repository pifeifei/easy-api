<?php

declare(strict_types=1);

namespace Pff\EasyApiTest\Unit;

use GuzzleHttp\Psr7\Response;
use GuzzleHttp\Psr7\Uri;
use Pff\EasyApi\API;
use Pff\EasyApi\Config;
use Pff\EasyApi\Exception\ClientException;
use Pff\EasyApi\Request\Request;
use Pff\EasyApi\Result;
use Pff\EasyApiTest\TestCase;

/**
 * @internal
 *
 * @coversNothing
 */
final class ResultTest extends TestCase
{
    protected Result $result;

    /**
     * @throws ClientException
     */
    protected function setUp(): void
    {
        $uri = new Uri('https://pifeifei.com/path/to/index.html');
        $options = [
            'proxy' => 'http://127.0.0.1:8888',
        ];
        $config = new Config([
            'config' => [
                'app_key' => 'app key string',
                'token' => 'token string',
            ],
            'request' => [
                'format' => API::RESPONSE_FORMAT_JSON,
            ],
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

    public function testResult(): void
    {
        $this->assertTrue($this->result->isSuccess());
        $this->assertInstanceOf(Request::class, $this->result->getRequest());
        $this->assertSame(200, $this->result->getStatusCode());
        $this->assertSame('{"foo":"foo","bar":true,"arr":{"a1":"v1","a2":"v2"}}', $this->result->toJson());
        $this->assertSame('{"foo":"foo","bar":true,"arr":{"a1":"v1","a2":"v2"}}', $this->result->getBody()->__toString());
        $this->assertSame('v1', $this->result->get('arr.a1'));
        $this->assertFalse($this->result->isEmpty());
        $this->assertTrue($this->result->has('foo'));
        $this->assertSame(3, $this->result->count());
        $this->result->clear(['bar', 'none']);
        $this->assertSame(2, $this->result->count());
    }
}
