<?php

namespace Pff\EasyApiTest\Unit\Signature;

use Pff\EasyApi\Contracts\SignatureInterface;
use Pff\EasyApi\Signature\ShaHmac256Signature;
use Pff\EasyApiTest\TestCase;

class ShaHmac256SignatureTest extends TestCase
{
    public function testShaHmac256Signature()
    {
        // Setup
        $string = 'this is a ShaHmac256 test.';
        $accessKeySecret = 'app secret string';
        $expected = '841a42794e852f75bff0b9876b827938f7d044574217e2280fd7ad81e80ab1c4';

        // Test
        $signature = new ShaHmac256Signature();

        // Assert
        static::assertInstanceOf(SignatureInterface::class, $signature);
        static::assertInstanceOf(ShaHmac256Signature::class, $signature);
        static::assertEquals('HMAC-SHA256', $signature->getMethod());
        static::assertEquals('1.0', $signature->getVersion());
        static::assertEquals('', $signature->getType());
//        static::assertEquals(
//            $expected,
//            $signature->sign($string, $accessKeySecret)
//        );
    }
}
