<?php

declare(strict_types=1);

namespace Pff\EasyApiTest\Unit\Concerns;

use Pff\EasyApiTest\TestCase;
use Pff\EasyApiTest\Unit\Concerns\stubs\DataTraitStub;

/**
 * @internal
 * @coversNothing
 */
final class DataTraitTest extends TestCase
{
    public function testDataTrait(): void
    {
        $data = new DataTraitStub([]);
        static::assertTrue($data->isEmpty());

        $data->set('foo', 'f');
        static::assertSame(1, $data->count());

        $data->set('bar', 'bar');
        static::assertSame(2, $data->count());
        static::assertSame('bar', $data->get('bar'));
    }

    public function testArrayAccess(): void
    {
        $data = new DataTraitStub([]);
        $data['foo'] = 'foo';
        static::assertSame('foo', $data->get('foo'));
        static::assertSame('foo', $data['foo']);

        static::assertTrue($data->has('foo'));
        unset($data['foo']);
        static::assertFalse($data->has('foo'));

        $data['bar'] = 'bar';
        foreach ($data as $item) {
            $iterator = true;
        }
        static::assertTrue($iterator);
    }

    public function testAdd(): void
    {
        $data = new DataTraitStub([]);
        $data->add($arr = ['foo' => 'foo', 'aa' => ['bb' => 'value'], 'a' => ['b' => ['cc' => 'value']]]);
        $data->add('a.b.c.d', 'value');
        $arr['a']['b']['c']['d'] = 'value';
        static::assertSame($arr, $data->all());
    }

    public function testDelete(): void
    {
        $data = new DataTraitStub([
            'foo' => 'foo',
            'aa' => ['bb' => 'value'],
            'a' => ['b' => ['cc' => 'value', 'c' => ['d' => 'value']]],
        ]);
        static::assertTrue($data->has('a.b.c.d'));
        $data->delete('a.b.c.d');
        static::assertFalse($data->has('a.b.c.d'));
        $data->delete(['a', 'aa']);
        static::assertFalse($data->has('a'));
        static::assertFalse($data->has('aa'));
    }

    public function testGet(): void
    {
        $data = new DataTraitStub([]);
        $data->add('a.b.c.d', 'value');
        $data->add('a.b.cc.d', 'value');
        static::assertSame('value', $data->get('a.b.c.d'));

        static::assertSame([
            'c' => ['d' => 'value'],
            'cc' => ['d' => 'value'],
        ], $data->get('a.b'));
    }

    public function testObject(): void
    {
        $data = new DataTraitStub([]);
        $data->foo = 'foo';
        static::assertSame('foo', $data->get('foo'));
        static::assertSame('foo', $data->foo);

        static::assertTrue($data->has('foo'));
        static::assertTrue(isset($data->foo));
        // phpstan-ignore-next-line
        $data->foo = null;
        static::assertFalse($data->has('foo'));
        static::assertFalse(isset($data->foo));
    }

    public function testArray(): void
    {
        $data = new DataTraitStub(['foo' => 'foo']);
        static::assertSame(['foo' => 'foo'], $data->toArray());
        static::assertSame(['foo' => 'foo'], $data->all());

        $data->clear('foo');
        static::assertEmpty($data->toArray());
        static::assertEmpty($data->all());

        $data->foo = 'xx';
        $data->bar = 'xx';
        static::assertNotEmpty($data->toArray());

        $data->clear(['foo', 'bar']);
        static::assertEmpty($data->toArray());
        static::assertEmpty($data->all());
    }

    public function testClear(): void
    {
        $data = new DataTraitStub(['foo' => 'foo', 'bar' => 'bar', 'foobar' => 1]);
        $data->clear('foo');
        static::assertSame(2, $data->count());

        $data->clear();
        static::assertSame([], $data->all());
    }

    public function testJson(): void
    {
        $data = new DataTraitStub(['foo' => 'foo', 'bar' => 'bar', 'foobar' => 1]);
        static::assertSame('{"foo":"foo","bar":"bar","foobar":1}', $data->toJson());
        static::assertSame(['foo' => 'foo', 'bar' => 'bar', 'foobar' => 1], $data->jsonSerialize());

        $data->bar = true;
        $data->foobar = 1.0;
        static::assertSame('{"foo":"foo","bar":true,"foobar":1}', $data->toJson());
        static::assertSame(['foo' => 'foo', 'bar' => true, 'foobar' => 1.0], $data->jsonSerialize());
        static::assertTrue($data->get('bar'));
    }

    /**
     * @dataProvider flattenData
     *
     * @param mixed $arr
     * @param mixed $val
     */
    public function testFlatten($arr, $val): void
    {
        $data = new DataTraitStub($arr);
        static::assertSame($val, $data->flatten());
    }

    public function flattenData()
    {
        return [
            [
                ['foo' => 'foo', 'bar' => 'bar'],
                ['foo' => 'foo', 'bar' => 'bar'],
            ],
            [
                ['foo' => 'foo', 'bar' => ['a' => ['b' => 'value']]],
                ['foo' => 'foo', 'bar.a.b' => 'value'],
            ],
        ];
    }
}
