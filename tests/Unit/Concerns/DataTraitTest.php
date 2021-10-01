<?php

namespace Pff\EasyApiTest\Unit\Concerns;

use Pff\EasyApiTest\TestCase;
use Pff\EasyApiTest\Unit\Concerns\stubs\DataTraitStub;

class DataTraitTest extends TestCase
{

    public function testDataTrait()
    {
        $data = new DataTraitStub([]);
        $this->assertTrue($data->isEmpty());

        $data->set('foo', 'f');
        $this->assertEquals(1, $data->count());

        $data->set('bar', 'bar');
        $this->assertEquals(2, $data->count());
        $this->assertEquals('bar', $data->get('bar'));
    }

    public function testArrayAccess()
    {
        $data = new DataTraitStub([]);
        $data['foo'] = 'foo';
        $this->assertEquals('foo', $data->get('foo'));
        $this->assertEquals('foo', $data['foo']);

        $this->assertTrue($data->has('foo'));
        unset($data['foo']);
        $this->assertFalse($data->has('foo'));

        $data['bar'] = 'bar';
        foreach ($data as $item) {
            $iterator = true;
        }
        $this->assertTrue($iterator);
    }

    public function testAdd()
    {
        $data = new DataTraitStub([]);
        $data->add('a.b.c.d', 'value');
        $data->add($arr = ['foo' => 'foo', 'aa' => ['bb' => 'value'], 'a' => ['b' => ['cc' => 'value']]]);
        $arr['a']['b']['c']['d'] = 'value';
        $this->assertEquals($arr, $data->all());
    }

    public function testDelete()
    {
        $data = new DataTraitStub([
            'foo' => 'foo',
            'aa' => ['bb' => 'value'],
            'a' => ['b' => ['cc' => 'value', 'c' => ['d' => 'value']]]
        ]);
        $this->assertTrue($data->has('a.b.c.d'));
        $data->delete('a.b.c.d');
        $this->assertFalse($data->has('a.b.c.d'));
        $data->delete(['a', 'aa']);
        $this->assertFalse($data->has('a'));
        $this->assertFalse($data->has('aa'));
    }

    public function testGet()
    {
        $data = new DataTraitStub([]);
        $data->add('a.b.c.d', 'value');
        $data->add('a.b.cc.d', 'value');
        $this->assertEquals('value', $data->get('a.b.c.d'));

        $this->assertEquals([
            'c' => ['d' => 'value'],
            'cc' => ['d' => 'value'],
        ], $data->get('a.b'));
    }

    public function testObject()
    {
        $data = new DataTraitStub([]);
        $data->foo = 'foo';
        $this->assertEquals('foo', $data->get('foo'));
        $this->assertEquals('foo', $data->foo);

        $this->assertTrue($data->has('foo'));
        $this->assertTrue(isset($data->foo));
        unset($data->foo);
        $this->assertFalse($data->has('foo'));
        $this->assertFalse(isset($data->foo));
    }

    public function testArray()
    {
        $data = new DataTraitStub(['foo' => 'foo']);
        $this->assertEquals(['foo' => 'foo'], $data->toArray());
        $this->assertEquals(['foo' => 'foo'], $data->all());

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

    public function testClear()
    {
        $data = new DataTraitStub(['foo' => 'foo', 'bar' => 'bar', "foobar" => 1]);
        $data->clear('foo');
        $this->assertEquals(2, $data->count());

        $data->clear();
        $this->assertEquals([], $data->all());
    }

    public function testJson()
    {
        $data = new DataTraitStub(['foo' => 'foo', 'bar' => 'bar', "foobar" => 1]);
        $this->assertEquals('{"foo":"foo","bar":"bar","foobar":1}', $data->toJson());
        $this->assertEquals(['foo' => 'foo', 'bar' => 'bar', "foobar" => 1], $data->jsonSerialize());

        $data->bar = true;
        $data->foobar = 1.0;
        $this->assertEquals('{"foo":"foo","bar":true,"foobar":1}', $data->toJson());
        $this->assertEquals(['foo' => 'foo', 'bar' => true, "foobar" => 1], $data->jsonSerialize());
        $this->assertTrue($data->get('bar'));
    }

    /**
     * @dataProvider flattenData
     */
    public function testFlatten($arr, $val)
    {
        $data = new DataTraitStub($arr);
        $this->assertEquals($val, $data->flatten());
    }

    public function flattenData()
    {
        return [
            [
                ['foo' => 'foo', 'bar' => 'bar'],
                ['foo' => 'foo', 'bar' => 'bar']
            ],
            [
                ['foo' => 'foo', 'bar' => ['a' => ['b' => 'value']]],
                ['foo' => 'foo', 'bar.a.b' => 'value']
            ],
        ];
    }
}
