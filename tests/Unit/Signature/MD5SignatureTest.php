<?php

namespace Pff\EasyApiTest\Unit\Signature;

use Pff\EasyApi\Contracts\SignatureInterface;
use Pff\EasyApi\Signature\MD5Signature;
use Pff\EasyApiTest\TestCase;

class MD5SignatureTest extends TestCase
{
    public function testShaHmac256Signature()
    {
        // Setup
        $string = 'this is a md5 test.';
        $accessKeySecret = 'accessKeySecret';
        $expected = 'cf374bf89710f3a93bcaa9e3a51fd539';

        // Test
        $signature = new MD5Signature();

        // Assert
        static::assertInstanceOf(SignatureInterface::class, $signature);
        static::assertInstanceOf(MD5Signature::class, $signature);
        static::assertEquals('MD5', $signature->getMethod());
        static::assertEquals('1.0', $signature->getVersion());
        static::assertEquals('', $signature->getType());
        static::assertEquals(
            $expected,
            $signature->sign($string, $accessKeySecret)
        );
    }
}
