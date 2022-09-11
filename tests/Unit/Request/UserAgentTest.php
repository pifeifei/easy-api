<?php

declare(strict_types=1);

namespace Pff\EasyApiTest\Unit\Request;

use Pff\EasyApi\Request\UserAgent;
use Pff\EasyApiTest\TestCase;

/**
 * @internal
 *
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

    public static function testClear(): void
    {
        UserAgent::append('test', '1.2.3');
        static::assertStringContainsString('Test/1.2.3', UserAgent::toString());

        UserAgent::clear();
        static::assertStringNotContainsString('Test/1.2.3', UserAgent::toString());
    }

    public static function testClean(): void
    {
        UserAgent::append('test', '1.2.3');
        UserAgent::append('test2', '2.3');
        static::assertStringContainsString('Test/1.2.3', UserAgent::toString());
        static::assertStringContainsString('Test2/2.3', UserAgent::toString());

        $arr = UserAgent::clean(['foo' => '1.2', 'PHP' => '7.4']);
        static::assertSame(['foo' => '1.2'], $arr);
    }

    public static function testUserAgentStringWithAppend(): void
    {
        $userAgent = UserAgent::toString([
            'append' => '1.0.0',
            'append2' => '2.0.0',
            'PHP' => '2.0.0',
        ]);

        static::assertStringStartsWith('EasyApi', $userAgent);
        static::assertStringContainsString('PHP/' . PHP_MAJOR_VERSION . '.' . PHP_MINOR_VERSION, $userAgent);
        static::assertStringEndsWith('Append/1.0.0 Append2/2.0.0', $userAgent);
    }

    public static function testUserAgentAppend(): void
    {
        UserAgent::append('Append', '1.0.0');
        static::assertStringEndsWith('Append/1.0.0', UserAgent::toString());

        UserAgent::append('Append', '1.2');
        static::assertStringEndsWith('Append/1.2', UserAgent::toString());
        static::assertStringNotContainsString('Append/1.0.0', UserAgent::toString());

        UserAgent::append('APPEND2', '1.2.5');
        static::assertStringEndsWith('Append2/1.2.5', UserAgent::toString());
    }

    public static function testUserAgentWith(): void
    {
        $userAgent = ['With' => '1.0.0', 'With2' => '2.0.0', 'With3' => true, 'With4'];
        UserAgent::with($userAgent);
        static::assertStringEndsWith('With/1.0.0 With2/2.0.0 With3 With4', UserAgent::toString());
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
