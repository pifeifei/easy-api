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
 *
 * @coversNothing
 */
final class ClientTraitTest extends TestCase
{
    public function testMethod(): void
    {
        $config = $this->getConfig();
        $client = new Client($config);
        $this->assertSame(API::METHOD_JSON, $client->getMethod());
        $this->assertSame(API::METHOD_POST, $client->requestMethod());

        $this->assertInstanceOf(Client::class, $client->setMethod(API::METHOD_XML));
        $this->assertSame(API::METHOD_XML, $client->getMethod());
        $this->assertSame(API::METHOD_POST, $client->requestMethod());
    }

    public function testSignature(): void
    {
        $config = $this->getConfig();
        $client = new Client($config);
        $signature = $client->getSignature();

        $this->assertInstanceOf(MD5Signature::class, $signature);

        $this->assertInstanceOf(Client::class, $client->setSignature($sign = ShaHmac256Signature::class));
        $this->assertInstanceOf($sign, $client->getSignature());

        $this->assertInstanceOf(Client::class, $client->setSignature($sign = new MD5Signature()));
        $this->assertInstanceOf(\get_class($sign), $client->getSignature());

        $this->assertInstanceOf(Client::class, $client->setSignature());
        $this->assertInstanceOf(\get_class($sign), $client->getSignature());
    }

    public function testIsTokenClient(): void
    {
        $config = $this->getConfig();
        $client = new Client($config);

        $this->assertFalse($client->isTokenClient());
        $this->assertInstanceOf(Client::class, $client->tokenClient(true));

        $this->assertTrue($client->isTokenClient());
    }
}
