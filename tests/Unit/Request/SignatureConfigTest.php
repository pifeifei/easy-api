<?php

namespace Pff\EasyApiTest\Unit\Request;

use Pff\EasyApi\Request\SignConfig;
use Pff\EasyApiTest\TestCase;

class SignatureConfigTest extends TestCase
{
    public function testCreate()
    {
        $config = [
            'key' => 'key string',
            'position' => 'position string',
            'appends' => [
                'k1' => 'v1',
                'foo' => 'foo'
            ]
        ];
        $s = SignConfig::create($config);
        $this->assertInstanceOf(SignConfig::class, $s);
        $this->assertEquals($config['key'], $s->getKey());
        $this->assertEquals($config['position'], $s->getPosition());
        $this->assertEquals($config['appends'], $s->getAppends());
        $s->setKey($k = 'abc def');
        $this->assertEquals($k, $s->getKey());
        $s->setPosition($k = 'abc def');
        $this->assertEquals($k, $s->getPosition());
    }
}
