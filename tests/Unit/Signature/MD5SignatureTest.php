<?php

declare(strict_types=1);

namespace Pff\EasyApiTest\Unit\Signature;

use Pff\EasyApi\Contracts\SignatureInterface;
use Pff\EasyApi\Signature\MD5Signature;
use Pff\EasyApiTest\TestCase;

/**
 * @internal
 * @coversNothing
 */
final class MD5SignatureTest extends TestCase
{
    public function testShaHmac256Signature(): void
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
        static::assertSame('MD5', $signature->getMethod());
        static::assertSame('1.0', $signature->getVersion());
        static::assertSame('', $signature->getType());
        static::assertSame(
            $expected,
            $signature->sign($string, $accessKeySecret)
        );
    }
}
