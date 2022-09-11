<?php

declare(strict_types=1);

namespace Pff\EasyApiTest\Unit\Request;

use Pff\EasyApi\Request\Parameters;
use Pff\EasyApiTest\TestCase;

/**
 * @internal
 *
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
        $this->assertSame(array_keys($arr), $p->keys());
        $this->assertSame(3, $p->count());

        $this->assertSame(123, $p->get('abc'));

        $this->assertSame('def', $p->get('none', 'def'));
        $this->assertFalse($p->has('none'));

        $this->assertInstanceOf(Parameters::class, $p->set('none', $v = 'def string'));
        $this->assertSame($v, $p->get('none', 'def'));
        $this->assertTrue($p->has('none'));
        $this->assertInstanceOf(Parameters::class, $p->remove('none'));
        $this->assertFalse($p->has('none'));
    }

    public function testParametersAdd(): void
    {
        $arr = ['foo' => 'foo'];
        $p = new Parameters($arr);
        $p->add(['bar' => 'bar', 'foobar' => 'foobar']); // @phpstan-ignore-line
        $this->assertTrue($p->has('bar'));
    }

    public function testParametersCount(): void
    {
        $arr = [];
        $p = new Parameters($arr);

        $this->assertSame(0, $p->count());
        $p->replace(['bar' => 'bar']);  // @phpstan-ignore-line
        $this->assertSame(1, $p->count());
    }
}
