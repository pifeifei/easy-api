<?php

declare(strict_types=1);

namespace Pff\EasyApiTest\Unit\Signature;

use Pff\EasyApi\Contracts\SignatureInterface;
use Pff\EasyApi\Signature\ShaHmac256WithRsaSignature;
use Pff\EasyApiTest\TestCase;

/**
 * @internal
 *
 * @coversNothing
 */
final class ShaHmac256WithRsaSignatureTest extends TestCase
{
    public function testShaHmac256Signature(): void
    {
        // Setup
        $string = 'this is a sha256 with RSA test.';
        $accessKeySecret = 'app secret string';
        $expected = 'hBpCeU6FL3W/8LmHa4J5OPfQRFdCF+IoD9etgegKscQ=';

        // Test
        $signature = new ShaHmac256WithRsaSignature();

        // Assert
        $this->assertInstanceOf(SignatureInterface::class, $signature);
        $this->assertInstanceOf(ShaHmac256WithRsaSignature::class, $signature);
        $this->assertSame('SHA256withRSA', $signature->getMethod());
        $this->assertSame('1.0', $signature->getVersion());
        $this->assertSame('PRIVATEKEY', $signature->getType());
        // TODO: 需要一个 openssl 证书
        // static::assertEquals(
        //     $expected,
        //     $signature->sign($string, $accessKeySecret)
        // );
    }
}
