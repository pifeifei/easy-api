<?php

declare(strict_types=1);

namespace Pff\EasyApiTest\Unit;

use Pff\EasyApi\Config;
use Pff\EasyApi\Contracts\ConfigInterface;
use Pff\EasyApiTest\TestCase;

/**
 * @internal
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

        static::assertInstanceOf(ConfigInterface::class, $config);
        static::assertSame($configure['access_key'], $config->get('access_key'));
        static::assertInstanceOf(ConfigInterface::class, $config->set('test', 123));
        static::assertSame(123, $config->get('test'));
        static::assertInstanceOf(ConfigInterface::class, $config->remove('test'));
        static::assertNull($config->get('test'));
        static::assertSame($configure, $config->all());
        $config->set('a.b.c', 1234);
        static::assertSame(1234, $config->get('a.b.c'));
    }
}
