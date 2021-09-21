<?php

namespace Pff\EasyApiTest\Unit;

use Pff\EasyApi\Config;
use Pff\EasyApi\Contracts\ConfigInterface;
use Pff\EasyApiTest\TestCase;

class ConfigTest extends TestCase
{
    public function testConfig()
    {
        $configure = [
            "access_key" => '1234567890abc',
            "access_secret" => "ABC123DEF456",
        ];
        $config = Config::create($configure);

        $this->assertInstanceOf(ConfigInterface::class, $config);
        $this->assertEquals($configure['access_key'], $config->get('access_key'));
        $this->assertInstanceOf(ConfigInterface::class, $config->set('test', 123));
        $this->assertEquals(123, $config->get('test'));
        $this->assertInstanceOf(ConfigInterface::class, $config->remove('test'));
        $this->assertNull($config->get('test'));
        $this->assertEquals($configure, $config->all());
        $config->set('a.b.c', ['d'=>1234]);
        $this->assertEquals(1234, $config->get('a.b.c.d'));
    }
}
