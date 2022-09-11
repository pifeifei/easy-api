<?php

declare(strict_types=1);

namespace Pff\EasyApiTest\Unit\Request;

use Pff\EasyApi\Request\SignConfig;
use Pff\EasyApiTest\TestCase;

/**
 * @internal
 *
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
        $this->assertInstanceOf(SignConfig::class, $s);
        $this->assertSame($config['key'], $s->getKey());
        $this->assertSame($config['position'], $s->getPosition());
        $this->assertSame($config['appends'], $s->getAppends());
        $s->setKey($k = 'abc def');
        $this->assertSame($k, $s->getKey());
        $s->setPosition($k = 'abc def');
        $this->assertSame($k, $s->getPosition());
    }
}
