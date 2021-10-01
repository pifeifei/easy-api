<?php

namespace Pff\EasyApiTest\Unit;

use GuzzleHttp\Client;
use Pff\EasyApiTest\TestCase;

class UserAgentTest extends TestCase
{
    public function testUserAgent()
    {
        $this->assertNull(null);

        $client = new Client();
//        RequestOptions::ALLOW_REDIRECTS
    }
}
