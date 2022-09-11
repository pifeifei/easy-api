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
 *
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

        $this->assertSame($this->defaultOptions(), $client->getOptions());
    }

    /**
     * @throws ClientException
     */
    public function testQuery(): void
    {
        $config = $this->getConfig();
        $client = new Client($config);

        $query = $client->getQuery();

        $this->assertInstanceOf(Parameters::class, $query);
        $this->assertEmpty($query->all());

        $this->assertInstanceOf(Client::class, $client->addQuery(['foo' => 'foo']));
        $this->assertSame(['foo' => 'foo'], $query->all());

        $this->assertInstanceOf(Client::class, $client->addQuery(['bar' => 'bar']));
        $this->assertSame(['foo' => 'foo', 'bar' => 'bar'], $query->all());

        $this->assertInstanceOf(Client::class, $client->addQuery('bar', 'foo'));
        $this->assertSame(['foo' => 'foo', 'bar' => 'foo'], $query->all());

        $this->assertInstanceOf(Client::class, $client->setQuery(['foo' => 'foo']));
        $this->assertSame(['foo' => 'foo'], $query->all());
        $query->clean();

        // delete start
        $query = $client->query();
        $this->assertEmpty($query->all()); // @phpstan-ignore-line

        $this->assertInstanceOf(Client::class, $client->query(['foo' => 'foo']));
        $this->assertSame(['foo' => 'foo'], $query->all()); // @phpstan-ignore-line

        $this->assertInstanceOf(Client::class, $client->query(['bar' => 'bar']));
        $this->assertSame(['foo' => 'foo', 'bar' => 'bar'], $query->all()); // @phpstan-ignore-line

        $this->assertInstanceOf(Client::class, $client->query(['bar' => 'foo']));
        $this->assertSame(['foo' => 'foo', 'bar' => 'foo'], $query->all()); // @phpstan-ignore-line

        $this->assertInstanceOf(Client::class, $client->query(['foo' => 'foo'], true));
        $this->assertSame(['foo' => 'foo'], $query->all()); // @phpstan-ignore-line
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

        $this->assertInstanceOf(Parameters::class, $data);
        $this->assertEmpty($data->all());

        $this->assertInstanceOf(Client::class, $client->addData(['foo' => 'foo']));
        $this->assertSame(['foo' => 'foo'], $data->all());

        $this->assertInstanceOf(Client::class, $client->addData(['bar' => 'bar']));
        $this->assertSame(['foo' => 'foo', 'bar' => 'bar'], $data->all());

        $this->assertInstanceOf(Client::class, $client->addData('bar', 'foo'));
        $this->assertSame(['foo' => 'foo', 'bar' => 'foo'], $data->all());

        $this->assertInstanceOf(Client::class, $client->setData(['foo' => 'foo']));
        $this->assertSame(['foo' => 'foo'], $data->all());
        $data->clean();

        // delete start
        $data = $client->data();
        $this->assertEmpty($data->all()); // @phpstan-ignore-line

        $this->assertInstanceOf(Client::class, $client->data(['foo' => 'foo']));
        $this->assertSame(['foo' => 'foo'], $data->all()); // @phpstan-ignore-line

        $this->assertInstanceOf(Client::class, $client->data(['bar' => 'bar']));
        $this->assertSame(['foo' => 'foo', 'bar' => 'bar'], $data->all()); // @phpstan-ignore-line

        $this->assertInstanceOf(Client::class, $client->data(['bar' => 'foo']));
        $this->assertSame(['foo' => 'foo', 'bar' => 'foo'], $data->all()); // @phpstan-ignore-line

        $this->assertInstanceOf(Client::class, $client->data(['foo' => 'foo'], true));
        $this->assertSame(['foo' => 'foo'], $data->all()); // @phpstan-ignore-line
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

        $this->assertInstanceOf(Headers::class, $headers);
        $this->assertEmpty($headers->all());

        $this->assertInstanceOf(Client::class, $client->addHeaders(['foo' => 'foo']));
        $this->assertSame(['foo' => ['foo']], $headers->all());

        $this->assertInstanceOf(Client::class, $client->addHeaders(['bar' => 'bar']));
        $this->assertSame(['foo' => ['foo'], 'bar' => ['bar']], $headers->all());

        $this->assertInstanceOf(Client::class, $client->setHeader('bar', 'foo'));
        $this->assertSame(['foo' => ['foo'], 'bar' => ['foo']], $headers->all());

        $this->assertInstanceOf(Client::class, $client->setHeaders(['foo' => 'foo']));
        $this->assertSame(['foo' => ['foo']], $headers->all());
        $headers->remove('foo');

        // delete start
        $headers = $client->headers();
        $this->assertEmpty($headers->all()); // @phpstan-ignore-line

        $this->assertInstanceOf(Client::class, $client->headers(['foo' => 'foo']));
        $this->assertSame(['foo' => ['foo']], $headers->all()); // @phpstan-ignore-line

        $this->assertInstanceOf(Client::class, $client->headers(['bar' => 'bar']));
        $this->assertSame(['foo' => ['foo'], 'bar' => ['bar']], $headers->all()); // @phpstan-ignore-line

        $this->assertInstanceOf(Client::class, $client->headers(['bar' => 'foo']));
        $this->assertSame(['foo' => ['foo'], 'bar' => ['foo']], $headers->all()); // @phpstan-ignore-line

        $this->assertInstanceOf(Client::class, $client->headers(['foo' => 'foo'], true)); // @phpstan-ignore-line
        $this->assertSame(['foo' => ['foo']], $headers->all()); // @phpstan-ignore-line
        // delete end
    }

    /**
     * @throws ClientException
     */
    public function testTimeout(): void
    {
        $config = $this->getConfig();
        $client = new Client($config);

        $this->assertInstanceOf(Client::class, $client->timeout(20));
        $this->assertSame(20.0, $client->getOptions()[RequestOptions::TIMEOUT]);

        $this->assertInstanceOf(Client::class, $client->timeoutMilliseconds(3500));
        $this->assertSame(3.5, $client->getOptions()[RequestOptions::TIMEOUT]);

        $this->assertInstanceOf(Client::class, $client->connectTimeout(15));
        $this->assertSame(15.0, $client->getOptions()[RequestOptions::CONNECT_TIMEOUT]);

        $this->assertInstanceOf(Client::class, $client->connectTimeoutMilliseconds(500));
        $this->assertSame(0.5, $client->getOptions()[RequestOptions::CONNECT_TIMEOUT]);
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
