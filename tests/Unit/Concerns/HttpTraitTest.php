<?php

declare(strict_types=1);

namespace Pff\EasyApiTest\Unit\Concerns;

use GuzzleHttp\RequestOptions;
use Pff\EasyApi\Clients\Client;
use Pff\EasyApi\Exception\ClientException;
use Pff\EasyApi\Request\Headers;
use Pff\EasyApi\Request\Parameters;
use Pff\EasyApiTest\TestCase;

/**
 * @internal
 * @coversNothing
 */
final class HttpTraitTest extends TestCase
{
    /**
     * @throws ClientException
     */
    public function testHttpTrait(): void
    {
        $config = $this->getConfig();
        $client = new Client($config);

        static::assertSame($this->defaultOptions(), $client->getOptions());
    }

    /**
     * @throws ClientException
     */
    public function testQuery(): void
    {
        $config = $this->getConfig();
        $client = new Client($config);

        $query = $client->getQuery();

        static::assertInstanceOf(Parameters::class, $query);
        static::assertEmpty($query->all());

        static::assertInstanceOf(Client::class, $client->addQuery(['foo' => 'foo']));
        static::assertSame(['foo' => 'foo'], $query->all());

        static::assertInstanceOf(Client::class, $client->addQuery(['bar' => 'bar']));
        static::assertSame(['foo' => 'foo', 'bar' => 'bar'], $query->all());

        static::assertInstanceOf(Client::class, $client->addQuery('bar', 'foo'));
        static::assertSame(['foo' => 'foo', 'bar' => 'foo'], $query->all());

        static::assertInstanceOf(Client::class, $client->setQuery(['foo' => 'foo']));
        static::assertSame(['foo' => 'foo'], $query->all());
        $query->clean();

        // delete start
        $query = $client->query();
        static::assertEmpty($query->all()); // @phpstan-ignore-line

        static::assertInstanceOf(Client::class, $client->query(['foo' => 'foo']));
        static::assertSame(['foo' => 'foo'], $query->all()); // @phpstan-ignore-line

        static::assertInstanceOf(Client::class, $client->query(['bar' => 'bar']));
        static::assertSame(['foo' => 'foo', 'bar' => 'bar'], $query->all()); // @phpstan-ignore-line

        static::assertInstanceOf(Client::class, $client->query(['bar' => 'foo']));
        static::assertSame(['foo' => 'foo', 'bar' => 'foo'], $query->all()); // @phpstan-ignore-line

        static::assertInstanceOf(Client::class, $client->query(['foo' => 'foo'], true));
        static::assertSame(['foo' => 'foo'], $query->all()); // @phpstan-ignore-line
        // delete end
    }

    /**
     * @throws ClientException
     */
    public function testData(): void
    {
        $config = $this->getConfig();
        $client = new Client($config);

        $data = $client->getData();

        static::assertInstanceOf(Parameters::class, $data);
        static::assertEmpty($data->all());

        static::assertInstanceOf(Client::class, $client->addData(['foo' => 'foo']));
        static::assertSame(['foo' => 'foo'], $data->all());

        static::assertInstanceOf(Client::class, $client->addData(['bar' => 'bar']));
        static::assertSame(['foo' => 'foo', 'bar' => 'bar'], $data->all());

        static::assertInstanceOf(Client::class, $client->addData('bar', 'foo'));
        static::assertSame(['foo' => 'foo', 'bar' => 'foo'], $data->all());

        static::assertInstanceOf(Client::class, $client->setData(['foo' => 'foo']));
        static::assertSame(['foo' => 'foo'], $data->all());
        $data->clean();

        // delete start
        $data = $client->data();
        static::assertEmpty($data->all()); // @phpstan-ignore-line

        static::assertInstanceOf(Client::class, $client->data(['foo' => 'foo']));
        static::assertSame(['foo' => 'foo'], $data->all()); // @phpstan-ignore-line

        static::assertInstanceOf(Client::class, $client->data(['bar' => 'bar']));
        static::assertSame(['foo' => 'foo', 'bar' => 'bar'], $data->all()); // @phpstan-ignore-line

        static::assertInstanceOf(Client::class, $client->data(['bar' => 'foo']));
        static::assertSame(['foo' => 'foo', 'bar' => 'foo'], $data->all()); // @phpstan-ignore-line

        static::assertInstanceOf(Client::class, $client->data(['foo' => 'foo'], true));
        static::assertSame(['foo' => 'foo'], $data->all()); // @phpstan-ignore-line
        // delete end
    }

    /**
     * @throws ClientException
     */
    public function testHeaders(): void
    {
        $config = $this->getConfig();
        $client = new Client($config);
        $headers = $client->getHeaders();

        static::assertInstanceOf(Headers::class, $headers);
        static::assertEmpty($headers->all());

        static::assertInstanceOf(Client::class, $client->addHeaders(['foo' => 'foo']));
        static::assertSame(['foo' => ['foo']], $headers->all());

        static::assertInstanceOf(Client::class, $client->addHeaders(['bar' => 'bar']));
        static::assertSame(['foo' => ['foo'], 'bar' => ['bar']], $headers->all());

        static::assertInstanceOf(Client::class, $client->setHeader('bar', 'foo'));
        static::assertSame(['foo' => ['foo'], 'bar' => ['foo']], $headers->all());

        static::assertInstanceOf(Client::class, $client->setHeaders(['foo' => 'foo']));
        static::assertSame(['foo' => ['foo']], $headers->all());
        $headers->remove('foo');

        // delete start
        $headers = $client->headers();
        static::assertEmpty($headers->all()); // @phpstan-ignore-line

        static::assertInstanceOf(Client::class, $client->headers(['foo' => 'foo']));
        static::assertSame(['foo' => ['foo']], $headers->all()); // @phpstan-ignore-line

        static::assertInstanceOf(Client::class, $client->headers(['bar' => 'bar']));
        static::assertSame(['foo' => ['foo'], 'bar' => ['bar']], $headers->all()); // @phpstan-ignore-line

        static::assertInstanceOf(Client::class, $client->headers(['bar' => 'foo']));
        static::assertSame(['foo' => ['foo'], 'bar' => ['foo']], $headers->all()); // @phpstan-ignore-line

        static::assertInstanceOf(Client::class, $client->headers(['foo' => 'foo'], true)); // @phpstan-ignore-line
        static::assertSame(['foo' => ['foo']], $headers->all()); // @phpstan-ignore-line
        // delete end
    }

    /**
     * @throws ClientException
     */
    public function testTimeout(): void
    {
        $config = $this->getConfig();
        $client = new Client($config);

        static::assertInstanceOf(Client::class, $client->timeout(20));
        static::assertSame(20.0, $client->getOptions()[RequestOptions::TIMEOUT]);

        static::assertInstanceOf(Client::class, $client->timeoutMilliseconds(3500));
        static::assertSame(3.5, $client->getOptions()[RequestOptions::TIMEOUT]);

        static::assertInstanceOf(Client::class, $client->connectTimeout(15));
        static::assertSame(15.0, $client->getOptions()[RequestOptions::CONNECT_TIMEOUT]);

        static::assertInstanceOf(Client::class, $client->connectTimeoutMilliseconds(500));
        static::assertSame(0.5, $client->getOptions()[RequestOptions::CONNECT_TIMEOUT]);
    }

    /**
     * @return array<string, mixed>
     */
    protected function defaultOptions(): array
    {
        return [
            'http_errors' => false,
            'connect_timeout' => Client::CONNECT_TIMEOUT,
            'timeout' => Client::TIMEOUT,
        ];
    }
}
