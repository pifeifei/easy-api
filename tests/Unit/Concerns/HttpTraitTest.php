<?php

declare(strict_types=1);

namespace Pff\EasyApiTest\Unit\Concerns;

use GuzzleHttp\RequestOptions;
use Pff\EasyApi\Clients\Client;
use Pff\EasyApiTest\TestCase;

/**
 * @internal
 * @coversNothing
 */
final class HttpTraitTest extends TestCase
{
    public function testHttpTrait(): void
    {
        $config = $this->getConfig();
        $client = new Client($config);

        static::assertSame($this->defaultOptions(), $client->getOptions());
    }

    public function testQuery(): void
    {
        $config = $this->getConfig();
        $client = new Client($config);
        $query = $client->query();

        static::assertEmpty($query->all());

        static::assertInstanceOf(Client::class, $client->query(['foo' => 'foo']));
        static::assertSame(['foo' => 'foo'], $query->all());

        static::assertInstanceOf(Client::class, $client->query(['bar' => 'bar']));
        static::assertSame(['foo' => 'foo', 'bar' => 'bar'], $query->all());

        static::assertInstanceOf(Client::class, $client->query(['bar' => 'foo']));
        static::assertSame(['foo' => 'foo', 'bar' => 'foo'], $query->all());

        static::assertInstanceOf(Client::class, $client->query(['foo' => 'foo'], true));
        static::assertSame(['foo' => 'foo'], $query->all());
    }

    public function testData(): void
    {
        $config = $this->getConfig();
        $client = new Client($config);
        $data = $client->data();

        static::assertEmpty($data->all());

        static::assertInstanceOf(Client::class, $client->data(['foo' => 'foo']));
        static::assertSame(['foo' => 'foo'], $data->all());

        static::assertInstanceOf(Client::class, $client->data(['bar' => 'bar']));
        static::assertSame(['foo' => 'foo', 'bar' => 'bar'], $data->all());

        static::assertInstanceOf(Client::class, $client->data(['bar' => 'foo']));
        static::assertSame(['foo' => 'foo', 'bar' => 'foo'], $data->all());

        static::assertInstanceOf(Client::class, $client->data(['foo' => 'foo'], true));
        static::assertSame(['foo' => 'foo'], $data->all());
    }

    public function testHeaders(): void
    {
        $config = $this->getConfig();
        $client = new Client($config);
        $headers = $client->headers();

        static::assertEmpty($headers->all());

        static::assertInstanceOf(Client::class, $client->headers(['foo' => 'foo']));
        static::assertSame(['foo' => ['foo']], $headers->all());

        static::assertInstanceOf(Client::class, $client->headers(['bar' => 'bar']));
        static::assertSame(['foo' => ['foo'], 'bar' => ['bar']], $headers->all());

        static::assertInstanceOf(Client::class, $client->headers(['bar' => 'foo']));
        static::assertSame(['foo' => ['foo'], 'bar' => ['foo']], $headers->all());

        static::assertInstanceOf(Client::class, $client->headers(['foo' => 'foo'], true));
        static::assertSame(['foo' => ['foo']], $headers->all());
    }

    public function testTimeout(): void
    {
        $config = $this->getConfig();
        $client = new Client($config);

        static::assertInstanceOf(Client::class, $client->timeout(20));
        static::assertSame(20, $client->getOptions()[RequestOptions::TIMEOUT]);

        static::assertInstanceOf(Client::class, $client->timeoutMilliseconds(3500));
        static::assertSame(3.5, $client->getOptions()[RequestOptions::TIMEOUT]);

        static::assertInstanceOf(Client::class, $client->connectTimeout(15));
        static::assertSame(15, $client->getOptions()[RequestOptions::CONNECT_TIMEOUT]);

        static::assertInstanceOf(Client::class, $client->connectTimeoutMilliseconds(500));
        static::assertSame(0.5, $client->getOptions()[RequestOptions::CONNECT_TIMEOUT]);
    }

    protected function defaultOptions()
    {
        return [
            'http_errors' => false,
            'connect_timeout' => Client::CONNECT_TIMEOUT,
            'timeout' => Client::TIMEOUT,
        ];
    }
}
