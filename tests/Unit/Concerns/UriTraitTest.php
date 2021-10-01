<?php

namespace Pff\EasyApiTest\Unit\Concerns;

use GuzzleHttp\Psr7\Uri;
use Pff\EasyApi\Clients\Client;
use Pff\EasyApiTest\TestCase;

class UriTraitTest extends TestCase
{
    public function testUri()
    {
        $config = $this->getConfig();
        $client = new Client($config);
        $uri = $client->uri();
        $this->assertInstanceOf(Uri::class, $uri);
        $this->assertEquals($config['request']['uri'], $uri->__toString());

        $client->path('test');
        $uri2 = $client->uri();
        $this->assertNotEquals($uri, $uri2);
    }

    public function testScheme()
    {
        $config = $this->getConfig();
        $client = new Client($config);

        $this->assertEquals('https', $client->uri()->getScheme());

        $client->scheme('http');
        $this->assertEquals('http', $client->uri()->getScheme());
    }

    public function testHost()
    {
        $config = $this->getConfig();
        $client = new Client($config);

        $this->assertEquals('httpbin.org', $client->uri()->getHost());

        $client->host('pff.sample.cn');
        $this->assertEquals('pff.sample.cn', $client->uri()->getHost());
    }

    public function testPath()
    {
        $config = $this->getConfig();
        $client = new Client($config);

        $this->assertEquals('/anything', $client->uri()->getPath());

        $client->path('path/to');
        $this->assertEquals('/anything/path/to', $client->uri()->getPath());

        $client->path('/path/to');
        $this->assertEquals('/path/to', $client->uri()->getPath());

        $client->path('../path/to');
        $this->assertEquals('/anything/../path/to', $client->uri()->getPath());
    }

    public function testPort()
    {
        $config = $this->getConfig();
        $client = new Client($config);

        $this->assertNull($client->uri()->getPort());

        $client->port(99);
        $this->assertEquals(99, $client->uri()->getPort());

        $client->port(80);
        $this->assertEquals(80, $client->uri()->getPort());
    }
}
