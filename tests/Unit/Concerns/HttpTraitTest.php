<?php

namespace Pff\EasyApiTest\Unit\Concerns;

use GuzzleHttp\RequestOptions;
use Pff\EasyApi\Clients\Client;
use Pff\EasyApiTest\TestCase;

class HttpTraitTest extends TestCase
{
    public function testHttpTrait()
    {
        $config = $this->getConfig();
        $client = new Client($config);

        $this->assertEquals($this->defaultOptions(), $client->getOptions());
    }

    protected function defaultOptions()
    {
        return [
            'http_errors' => false,
            'connect_timeout' => Client::CONNECT_TIMEOUT,
            'timeout' => Client::TIMEOUT
        ];
    }

    public function testQuery()
    {
        $config = $this->getConfig();
        $client = new Client($config);
        $query = $client->query();

        $this->assertEmpty($query->all());

        $this->assertInstanceOf(Client::class, $client->query(['foo' => 'foo']));
        $this->assertEquals(['foo' => 'foo'], $query->all());

        $this->assertInstanceOf(Client::class, $client->query(['bar' => 'bar']));
        $this->assertEquals(['foo' => 'foo', 'bar' => 'bar'], $query->all());

        $this->assertInstanceOf(Client::class, $client->query(['bar' => 'foo']));
        $this->assertEquals(['foo' => 'foo', 'bar' => 'foo'], $query->all());

        $this->assertInstanceOf(Client::class, $client->query(['foo' => 'foo'], true));
        $this->assertEquals(['foo' => 'foo'], $query->all());
    }

    public function testData()
    {
        $config = $this->getConfig();
        $client = new Client($config);
        $data = $client->data();

        $this->assertEmpty($data->all());

        $this->assertInstanceOf(Client::class, $client->data(['foo' => 'foo']));
        $this->assertEquals(['foo' => 'foo'], $data->all());

        $this->assertInstanceOf(Client::class, $client->data(['bar' => 'bar']));
        $this->assertEquals(['foo' => 'foo', 'bar' => 'bar'], $data->all());

        $this->assertInstanceOf(Client::class, $client->data(['bar' => 'foo']));
        $this->assertEquals(['foo' => 'foo', 'bar' => 'foo'], $data->all());

        $this->assertInstanceOf(Client::class, $client->data(['foo' => 'foo'], true));
        $this->assertEquals(['foo' => 'foo'], $data->all());
    }

    public function testHeaders()
    {
        $config = $this->getConfig();
        $client = new Client($config);
        $headers = $client->headers();

        $this->assertEmpty($headers->all());

        $this->assertInstanceOf(Client::class, $client->headers(['foo' => 'foo']));
        $this->assertEquals(['foo' => ['foo']], $headers->all());

        $this->assertInstanceOf(Client::class, $client->headers(['bar' => 'bar']));
        $this->assertEquals(['foo' => ['foo'], 'bar' => ['bar']], $headers->all());

        $this->assertInstanceOf(Client::class, $client->headers(['bar' => 'foo']));
        $this->assertEquals(['foo' => ['foo'], 'bar' => ['foo']], $headers->all());

        $this->assertInstanceOf(Client::class, $client->headers(['foo' => 'foo'], true));
        $this->assertEquals(['foo' => ['foo']], $headers->all());
    }

    public function testTimeout()
    {
        $config = $this->getConfig();
        $client = new Client($config);

        $this->assertInstanceOf(Client::class, $client->timeout(20));
        $this->assertEquals(20, $client->getOptions()[RequestOptions::TIMEOUT]);

        $this->assertInstanceOf(Client::class, $client->timeoutMilliseconds(3500));
        $this->assertEquals(3.5, $client->getOptions()[RequestOptions::TIMEOUT]);

        $this->assertInstanceOf(Client::class, $client->connectTimeout(15));
        $this->assertEquals(15, $client->getOptions()[RequestOptions::CONNECT_TIMEOUT]);

        $this->assertInstanceOf(Client::class, $client->connectTimeoutMilliseconds(500));
        $this->assertEquals(0.5, $client->getOptions()[RequestOptions::CONNECT_TIMEOUT]);
    }
}
