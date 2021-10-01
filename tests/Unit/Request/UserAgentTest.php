<?php

namespace Pff\EasyApiTest\Unit\Request;

use Pff\EasyApi\Request\UserAgent;
use Pff\EasyApiTest\TestCase;

class UserAgentTest extends TestCase
{
    public static function testUserAgentString()
    {
        $userAgent = UserAgent::toString();
        self::assertStringStartsWith('EasyApi', $userAgent);
    }

    public static function testUserAgentStringWithAppend()
    {
        $userAgent = UserAgent::toString([
            'Append' => '1.0.0',
            'Append2' => '2.0.0',
            'PHP' => '2.0.0',
        ]);

        self::assertStringStartsWith('EasyApi', $userAgent);
        self::assertStringEndsWith('Append/1.0.0 Append2/2.0.0', $userAgent);
    }

    public static function testUserAgentAppend()
    {
        UserAgent::append('Append', '1.0.0');
        self::assertStringEndsWith('Append/1.0.0', UserAgent::toString());
    }

    public static function testUserAgentWith()
    {
        UserAgent::with([
            'With' => '1.0.0',
            'With2' => '2.0.0',
            'With3',
            'With4' => null
        ]);
        self::assertStringEndsWith('With/1.0.0 With2/2.0.0 With3 With4', UserAgent::toString());
    }

    public static function testGuard()
    {
        UserAgent::append('PHP', '7.x');
        self::assertStringEndsNotWith('PHP/7.x', UserAgent::toString());
        UserAgent::append('Client', '1.x-append');
        self::assertStringEndsNotWith('Client/1.x-append', UserAgent::toString());
    }

    public static function testGuardWithValueEmpty()
    {
        UserAgent::append('Append');
        self::assertStringEndsWith('Append', UserAgent::toString());
    }

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
}
