<?php

declare(strict_types=1);

namespace Pff\EasyApiTest\Unit\Concerns;

use GuzzleHttp\Psr7\Uri;
use Pff\EasyApi\Clients\Client;
use Pff\EasyApi\Exception\ClientException;
use Pff\EasyApiTest\TestCase;

/**
 * @internal
 *
 * @coversNothing
 */
final class UriTraitTest extends TestCase
{
    /**
     * @throws ClientException
     */
    public function testUri(): void
    {
        $config = $this->getConfig();
        $client = new Client($config);
        $uri = $client->uri();
        $this->assertInstanceOf(Uri::class, $uri);
        $this->assertSame($config['request']['uri'], $uri->__toString()); // @phpstan-ignore-line

        $client->path('test');
        $uri2 = $client->uri();
        $this->assertNotSame($uri, $uri2);
    }

    /**
     * @throws ClientException
     */
    public function testScheme(): void
    {
        $config = $this->getConfig();
        $client = new Client($config);

        $this->assertSame('https', $client->uri()->getScheme());

        $client->scheme('http');
        $this->assertSame('http', $client->uri()->getScheme());
    }

    /**
     * @throws ClientException
     */
    public function testHost(): void
    {
        $config = $this->getConfig();
        $client = new Client($config);

        $this->assertSame('httpbin.org', $client->uri()->getHost());

        $client->host('pff.sample.cn');
        $this->assertSame('pff.sample.cn', $client->uri()->getHost());
    }

    /**
     * @throws ClientException
     */
    public function testPath(): void
    {
        $config = $this->getConfig();
        $client = new Client($config);

        $this->assertSame('/anything', $client->uri()->getPath());

        $client->path('path/to');
        $this->assertSame('/anything/path/to', $client->uri()->getPath());

        $client->path('/path/to');
        $this->assertSame('/path/to', $client->uri()->getPath());

        $client->path('../path/to');
        $this->assertSame('/anything/../path/to', $client->uri()->getPath());
    }

    /**
     * @throws ClientException
     */
    public function testPort(): void
    {
        $config = $this->getConfig();
        $client = new Client($config);

        $this->assertNull($client->uri()->getPort());

        $client->port(99);
        $this->assertSame(99, $client->uri()->getPort());

        $client->port(80);
        $this->assertSame(80, $client->uri()->getPort());
    }
}
