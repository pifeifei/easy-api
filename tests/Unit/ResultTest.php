<?php

declare(strict_types=1);

namespace Pff\EasyApiTest\Unit;

use GuzzleHttp\Psr7\Response;
use GuzzleHttp\Psr7\Uri;
use Pff\EasyApi\API;
use Pff\EasyApi\Config;
use Pff\EasyApi\Request\Request;
use Pff\EasyApi\Result;
use Pff\EasyApiTest\TestCase;

/**
 * @internal
 * @coversNothing
 */
final class ResultTest extends TestCase
{
    /**
     * @var Result
     */
    protected $result;

    protected function setUp(): void
    {
        $uri = new Uri('http://pifeifei.com/path/to/index.html');
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

    protected function tearDown(): void
    {
        $this->result = null;
    }

    public function testResult(): void
    {
        static::assertTrue($this->result->isSuccess());
        static::assertInstanceOf(Request::class, $this->result->getRequest());
        static::assertSame(200, $this->result->getStatusCode());
        static::assertSame('{"foo":"foo","bar":true,"arr":{"a1":"v1","a2":"v2"}}', $this->result->toJson());
        static::assertSame('{"foo":"foo","bar":true,"arr":{"a1":"v1","a2":"v2"}}', $this->result->getBody()->__toString());
        static::assertSame('v1', $this->result->get('arr.a1'));
        static::assertFalse($this->result->isEmpty());
        static::assertTrue($this->result->has('foo'));
        static::assertSame(3, $this->result->count());
        $this->result->clear(['bar', 'none']);
        static::assertSame(2, $this->result->count());
    }
}
