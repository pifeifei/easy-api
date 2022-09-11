<?php

declare(strict_types=1);

namespace Pff\EasyApiTest\Unit\Signature;

use Pff\EasyApi\Contracts\SignatureInterface;
use Pff\EasyApi\Signature\ShaHmac256Signature;
use Pff\EasyApiTest\TestCase;

/**
 * @internal
 *
 * @coversNothing
 */
final class ShaHmac256SignatureTest extends TestCase
{
    public function testShaHmac256Signature(): void
    {
        // Setup
        $string = 'this is a ShaHmac256 test.';
        $accessKeySecret = 'app secret string';
        $expected = '841a42794e852f75bff0b9876b827938f7d044574217e2280fd7ad81e80ab1c4';

        // Test
        $signature = new ShaHmac256Signature();

        // Assert
        $this->assertInstanceOf(SignatureInterface::class, $signature);
        $this->assertInstanceOf(ShaHmac256Signature::class, $signature);
        $this->assertSame('HMAC-SHA256', $signature->getMethod());
        $this->assertSame('1.0', $signature->getVersion());
        $this->assertSame('', $signature->getType());
//        static::assertEquals(
//            $expected,
//            $signature->sign($string, $accessKeySecret)
//        );
    }
}
