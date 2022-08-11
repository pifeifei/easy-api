<?php

declare(strict_types=1);

namespace Pff\EasyApiTest\Unit\Concerns;

use Pff\EasyApi\API;
use Pff\EasyApi\Clients\Client;
use Pff\EasyApi\Signature\MD5Signature;
use Pff\EasyApi\Signature\ShaHmac256Signature;
use Pff\EasyApiTest\TestCase;

/**
 * @internal
 * @coversNothing
 */
final class ClientTraitTest extends TestCase
{
    public function testMethod(): void
    {
        $config = $this->getConfig();
        $client = new Client($config);
        static::assertSame(API::METHOD_JSON, $client->method());
        static::assertSame(API::METHOD_POST, $client->requestMethod());

        static::assertInstanceOf(Client::class, $client->method(API::METHOD_XML));
        static::assertSame(API::METHOD_XML, $client->method());
        static::assertSame(API::METHOD_POST, $client->requestMethod());
    }

    public function testSignature(): void
    {
        $config = $this->getConfig();
        $client = new Client($config);
        $signature = $client->getSignature();

        static::assertInstanceOf(MD5Signature::class, $signature);

        static::assertInstanceOf(Client::class, $client->setSignature($sign = ShaHmac256Signature::class));
        static::assertInstanceOf($sign, $client->getSignature());

        static::assertInstanceOf(Client::class, $client->setSignature($sign = new MD5Signature()));
        static::assertInstanceOf(\get_class($sign), $client->getSignature());

        static::assertInstanceOf(Client::class, $client->setSignature());
        static::assertInstanceOf(\get_class($sign), $client->getSignature());
    }

    public function testIsTokenClient(): void
    {
        $config = $this->getConfig();
        $client = new Client($config);

        static::assertFalse($client->tokenClient());
        static::assertInstanceOf(Client::class, $client->tokenClient(true));

        static::assertTrue($client->tokenClient());
    }
}
