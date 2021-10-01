<?php

namespace Pff\EasyApiTest\Unit\Signature;

use Pff\EasyApi\Contracts\SignatureInterface;
use Pff\EasyApi\Signature\ShaHmac256WithRsaSignature;
use Pff\EasyApiTest\TestCase;

class ShaHmac256WithRsaSignatureTest extends TestCase
{
    public function testShaHmac256Signature()
    {
        // Setup
        $string = 'this is a sha256 with RSA test.';
        $accessKeySecret = 'app secret string';
        $expected = 'hBpCeU6FL3W/8LmHa4J5OPfQRFdCF+IoD9etgegKscQ=';

        // Test
        $signature = new ShaHmac256WithRsaSignature();

        // Assert
        static::assertInstanceOf(SignatureInterface::class, $signature);
        static::assertInstanceOf(ShaHmac256WithRsaSignature::class, $signature);
        static::assertEquals('SHA256withRSA', $signature->getMethod());
        static::assertEquals('1.0', $signature->getVersion());
        static::assertEquals('PRIVATEKEY', $signature->getType());
//        TODO: 需要一个 openssl 证书
//        static::assertEquals(
//            $expected,
//            $signature->sign($string, $accessKeySecret)
//        );
    }
}
