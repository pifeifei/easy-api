<?php

declare(strict_types=1);

namespace Pff\EasyApiTest\Unit\Request;

use Pff\EasyApi\Request\Parameters;
use Pff\EasyApiTest\TestCase;

/**
 * @internal
 * @coversNothing
 */
final class ParametersTest extends TestCase
{
    public function testParameters(): void
    {
        $arr = [
            'foo' => 'foo',
            'abc' => 123,
            'arr' => [
                'foo' => 'foo',
                'bar' => 'bar',
            ],
        ];
        $p = new Parameters($arr);
        static::assertSame(array_keys($arr), $p->keys());
        static::assertSame(3, $p->count());

        static::assertSame(123, $p->get('abc'));

        static::assertSame('def', $p->get('none', 'def'));
        static::assertFalse($p->has('none'));

        static::assertInstanceOf(Parameters::class, $p->set('none', $v = 'def string'));
        static::assertSame($v, $p->get('none', 'def'));
        static::assertTrue($p->has('none'));
        static::assertInstanceOf(Parameters::class, $p->remove('none'));
        static::assertFalse($p->has('none'));
    }

    public function testParametersAdd(): void
    {
        $arr = ['foo' => 'foo'];
        $p = new Parameters($arr);
        $p->add(['bar' => 'bar', 'foobar' => 'foobar']); // @phpstan-ignore-line
        static::assertTrue($p->has('bar'));
    }

    public function testParametersCount(): void
    {
        $arr = [];
        $p = new Parameters($arr);

        static::assertSame(0, $p->count());
        $p->replace(['bar' => 'bar']);  // @phpstan-ignore-line
        static::assertSame(1, $p->count());
    }
}
