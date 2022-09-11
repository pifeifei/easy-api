<?php

declare(strict_types=1);

namespace Pff\EasyApiTest\Unit\Concerns;

use Pff\EasyApi\Exception\ClientException;
use Pff\EasyApiTest\TestCase;
use Pff\EasyApiTest\Unit\Concerns\stubs\DataTraitStub;

/**
 * @internal
 *
 * @coversNothing
 */
final class DataTraitTest extends TestCase
{
    public function testDataTrait(): void
    {
        $data = new DataTraitStub([]);
        $this->assertTrue($data->isEmpty());

        $data->set('foo', 'f');
        $this->assertSame(1, $data->count());

        $data->set('bar', 'bar');
        $this->assertSame(2, $data->count());
        $this->assertSame('bar', $data->get('bar'));
    }

    public function testArrayAccess(): void
    {
        $data = new DataTraitStub([]);
        $data['foo'] = 'foo';
        $this->assertSame('foo', $data->get('foo'));
        $this->assertSame('foo', $data['foo']);

        $this->assertTrue($data->has('foo'));
        unset($data['foo']);
        $this->assertFalse($data->has('foo'));

        $iterator = false;
        $data['bar'] = 'bar';
        foreach ($data as $item) {
            $iterator = true;
        }
        $this->assertTrue($iterator);
    }

    public function testAdd(): void
    {
        $data = new DataTraitStub([]);
        $data->add($arr = ['foo' => 'foo', 'aa' => ['bb' => 'value'], 'a' => ['b' => ['cc' => 'value']]]);
        $data->add('a.b.c.d', 'value');
        $arr['a']['b']['c']['d'] = 'value';
        $this->assertSame($arr, $data->all());
    }

    public function testDelete(): void
    {
        $data = new DataTraitStub([
            'foo' => 'foo',
            'aa' => ['bb' => 'value'],
            'a' => ['b' => ['cc' => 'value', 'c' => ['d' => 'value']]],
        ]);
        $this->assertTrue($data->has('a.b.c.d'));
        $data->delete('a.b.c.d');
        $this->assertFalse($data->has('a.b.c.d'));
        $data->delete(['a', 'aa']);
        $this->assertFalse($data->has('a'));
        $this->assertFalse($data->has('aa'));
    }

    public function testGet(): void
    {
        $data = new DataTraitStub([]);
        $data->add('a.b.c.d', 'value');
        $data->add('a.b.cc.d', 'value');
        $this->assertSame('value', $data->get('a.b.c.d'));

        $this->assertSame([
            'c' => ['d' => 'value'],
            'cc' => ['d' => 'value'],
        ], $data->get('a.b'));
    }

    public function testObject(): void
    {
        $data = new DataTraitStub([]);
        $data->foo = 'foo';
        $this->assertSame('foo', $data->get('foo'));
        $this->assertSame('foo', $data->foo);

        $this->assertTrue($data->has('foo'));
        $this->assertTrue(isset($data->foo));

        $data->foo = null; // @phpstan-ignore-line
        $this->assertFalse($data->has('foo'));
        $this->assertFalse(isset($data->foo));
    }

    public function testArray(): void
    {
        $data = new DataTraitStub(['foo' => 'foo']);
        $this->assertSame(['foo' => 'foo'], $data->toArray());
        $this->assertSame(['foo' => 'foo'], $data->all());

        $data->clear('foo');
        $this->assertEmpty($data->toArray());
        $this->assertEmpty($data->all());

        $data->foo = 'xx';
        $data->bar = 'xx';
        $this->assertNotEmpty($data->toArray());

        $data->clear(['foo', 'bar']);
        $this->assertEmpty($data->toArray());
        $this->assertEmpty($data->all());
    }

    public function testClear(): void
    {
        $data = new DataTraitStub(['foo' => 'foo', 'bar' => 'bar', 'foobar' => 1]);
        $data->clear('foo');
        $this->assertSame(2, $data->count());

        $data->clear();
        $this->assertSame([], $data->all());
    }

    /**
     * @throws ClientException
     */
    public function testJson(): void
    {
        $data = new DataTraitStub(['foo' => 'foo', 'bar' => 'bar', 'foobar' => 1]);
        $this->assertSame('{"foo":"foo","bar":"bar","foobar":1}', $data->toJson());
        $this->assertSame(['foo' => 'foo', 'bar' => 'bar', 'foobar' => 1], $data->jsonSerialize());

        $data->bar = 'true';
        $data->foobar = '1.0';
        $this->assertSame('{"foo":"foo","bar":"true","foobar":"1.0"}', $data->toJson());
        $this->assertSame(['foo' => 'foo', 'bar' => 'true', 'foobar' => '1.0'], $data->jsonSerialize());
        $this->assertSame('true', $data->get('bar'));
    }

    /**
     * @dataProvider flattenData
     *
     * @param mixed $arr
     * @param mixed $val
     */
    public function testFlatten($arr, $val): void
    {
        $data = new DataTraitStub($arr); // @phpstan-ignore-line
        $this->assertSame($val, $data->flatten());
    }

    /**
     * @return array<array<int, mixed>>
     */
    public function flattenData(): array
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
