<?php

declare(strict_types=1);

namespace Pff\EasyApiTest\Unit;

use Pff\EasyApi\Config;
use Pff\EasyApi\Contracts\ConfigInterface;
use Pff\EasyApiTest\TestCase;

/**
 * @internal
 *
 * @coversNothing
 */
final class ConfigTest extends TestCase
{
    public function testConfig(): void
    {
        $configure = [
            'access_key' => '1234567890abc',
            'access_secret' => 'ABC123DEF456',
        ];
        $config = Config::create($configure);

        $this->assertInstanceOf(ConfigInterface::class, $config);
        $this->assertSame($configure['access_key'], $config->get('access_key'));
        $this->assertInstanceOf(ConfigInterface::class, $config->set('test', 123));
        $this->assertSame(123, $config->get('test'));
        $this->assertInstanceOf(ConfigInterface::class, $config->remove('test'));
        $this->assertNull($config->get('test'));
        $this->assertSame($configure, $config->all());
        $config->set('a.b.c', 1234);
        $this->assertSame(1234, $config->get('a.b.c'));
    }
}
