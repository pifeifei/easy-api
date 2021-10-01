<?php

namespace Concerns;

use Pff\EasyApi\API;
use Pff\EasyApi\Clients\Client;
use Pff\EasyApi\Signature\MD5Signature;
use Pff\EasyApi\Signature\ShaHmac256Signature;
use Pff\EasyApiTest\TestCase;

class ClientTraitTest extends TestCase
{
    public function testMethod()
    {
        $config = $this->getConfig();
        $client = new Client($config);
        $this->assertEquals(API::METHOD_JSON, $client->method());
        $this->assertEquals(API::METHOD_POST, $client->requestMethod());

        $this->assertInstanceOf(Client::class, $client->method(API::METHOD_XML));
        $this->assertEquals(API::METHOD_XML, $client->method());
        $this->assertEquals(API::METHOD_POST, $client->requestMethod());
    }

    public function testSignature()
    {
        $config = $this->getConfig();
        $client = new Client($config);
        $signature = $client->getSignature();

        $this->assertInstanceOf(MD5Signature::class, $signature);

        $this->assertInstanceOf(Client::class, $client->setSignature($sign = ShaHmac256Signature::class));
        $this->assertInstanceOf($sign, $client->getSignature());

        $this->assertInstanceOf(Client::class, $client->setSignature($sign = new MD5Signature()));
        $this->assertInstanceOf(get_class($sign), $client->getSignature());

        $this->assertInstanceOf(Client::class, $client->setSignature());
        $this->assertInstanceOf(get_class($sign), $client->getSignature());
    }

    public function testIsTokenClient()
    {
        $config = $this->getConfig();
        $client = new Client($config);

        $this->assertFalse($client->tokenClient());
        $this->assertInstanceOf(Client::class, $client->tokenClient(true));

        $this->assertTrue($client->tokenClient());
    }
}
