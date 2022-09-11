<?php

declare(strict_types=1);

namespace Pff\EasyApiTest\Unit\Request;

use GuzzleHttp\Psr7\Uri;
use Pff\EasyApi\API;
use Pff\EasyApi\Config;
use Pff\EasyApi\Contracts\ConfigInterface;
use Pff\EasyApi\Request\Request;
use Pff\EasyApiTest\TestCase;

/**
 * @internal
 *
 * @coversNothing
 */
final class RequestTest extends TestCase
{
    public function testRequest(): void
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
        $request = new Request('GET', $uri, $options, $config);
        $this->assertSame('GET', $request->getMethod());
        $this->assertSame(API::RESPONSE_FORMAT_JSON, $request->format());
        $this->assertInstanceOf(Uri::class, $request->getUri());
        $this->assertInstanceOf(ConfigInterface::class, $request->getConfig());
        $this->assertSame($options, $request->getOptions());
    }
}
