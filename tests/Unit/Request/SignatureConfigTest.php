<?php

declare(strict_types=1);

namespace Pff\EasyApiTest\Unit\Request;

use Pff\EasyApi\Request\SignConfig;
use Pff\EasyApiTest\TestCase;

/**
 * @internal
 * @coversNothing
 */
final class SignatureConfigTest extends TestCase
{
    public function testCreate(): void
    {
        $config = [
            'key' => 'key string',
            'position' => 'position string',
            'appends' => [
                'k1' => 'v1',
                'foo' => 'foo',
            ],
        ];
        $s = SignConfig::create($config);
        static::assertInstanceOf(SignConfig::class, $s);
        static::assertSame($config['key'], $s->getKey());
        static::assertSame($config['position'], $s->getPosition());
        static::assertSame($config['appends'], $s->getAppends());
        $s->setKey($k = 'abc def');
        static::assertSame($k, $s->getKey());
        $s->setPosition($k = 'abc def');
        static::assertSame($k, $s->getPosition());
    }
}
