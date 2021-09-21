<?php

namespace Pff\EasyApiTest\Unit;

use GuzzleHttp\Client;
use GuzzleHttp\RequestOptions;
use Pff\EasyApi\Request\UserAgent;
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
