<?php

declare(strict_types=1);

namespace Pff\EasyApiTest\Unit;

use GuzzleHttp\Client;
use Pff\EasyApiTest\TestCase;

/**
 * @internal
 * @coversNothing
 */
final class UserAgentTest extends TestCase
{
    public function testUserAgent(): void
    {
        static::assertNull(null);

        $client = new Client();
//        RequestOptions::ALLOW_REDIRECTS
    }
}
