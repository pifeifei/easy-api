<?php

declare(strict_types=1);

namespace Pff\EasyApiTest\Unit\Request;

use Pff\EasyApi\Request\UserAgent;
use Pff\EasyApiTest\TestCase;

/**
 * @internal
 * @coversNothing
 */
final class UserAgentTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        UserAgent::clear();
    }

    protected function tearDown(): void
    {
        parent::tearDown();
        UserAgent::clear();
    }

    public static function testUserAgentString(): void
    {
        $userAgent = UserAgent::toString();
        static::assertStringStartsWith('EasyApi', $userAgent);
    }

    public static function testUserAgentStringWithAppend(): void
    {
        $userAgent = UserAgent::toString([
            'Append' => '1.0.0',
            'Append2' => '2.0.0',
            'PHP' => '2.0.0',
        ]);

        static::assertStringStartsWith('EasyApi', $userAgent);
        static::assertStringEndsWith('Append/1.0.0 Append2/2.0.0', $userAgent);
    }

    public static function testUserAgentAppend(): void
    {
        UserAgent::append('Append', '1.0.0');
        static::assertStringEndsWith('Append/1.0.0', UserAgent::toString());
    }

    public static function testUserAgentWith(): void
    {
        UserAgent::with(['With' => '1.0.0', 'With2' => '2.0.0', 'With3']);
        static::assertStringEndsWith('With/1.0.0 With2/2.0.0 With3', UserAgent::toString());
    }

    public static function testGuard(): void
    {
        UserAgent::append('PHP', '7.x');
        static::assertStringEndsNotWith('PHP/7.x', UserAgent::toString());
        UserAgent::append('Client', '1.x-append');
        static::assertStringEndsNotWith('Client/1.x-append', UserAgent::toString());
    }

    public static function testGuardWithValueEmpty(): void
    {
        UserAgent::append('Append');
        static::assertStringEndsWith('Append', UserAgent::toString());
    }
}
