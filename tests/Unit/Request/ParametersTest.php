<?php

namespace Pff\EasyApiTest\Unit\Request;

use Pff\EasyApi\Request\Parameters;
use Pff\EasyApiTest\TestCase;

class ParametersTest extends TestCase
{
    public function testParameters()
    {
        $arr = [
            'foo' => 'foo',
            'abc' => 123,
            'arr' => [
                'foo' => 'foo',
                'bar' => 'bar'
            ]
        ];
        $p = new Parameters($arr);
        $this->assertEquals(array_keys($arr), $p->keys());
        $this->assertEquals(3, $p->count());

        $this->assertEquals(123, $p->get('abc'));

        $this->assertEquals('def', $p->get('none', 'def'));
        $this->assertFalse($p->has('none'));

        $this->assertInstanceOf(Parameters::class, $p->set('none', $v = 'def string'));
        $this->assertEquals($v, $p->get('none', 'def'));
        $this->assertTrue($p->has('none'));
        $this->assertInstanceOf(Parameters::class, $p->remove('none'));
        $this->assertFalse($p->has('none'));

    }


    public function testParametersAdd()
    {
        $arr = ['foo' => 'foo'];
        $p = new Parameters($arr);
        $p->add($arr2 = ['bar' => 'bar']);
        $this->assertTrue($p->has('bar'));
    }

    public function testParametersCount()
    {
        $arr = [];
        $p = new Parameters($arr);

        $this->assertEquals(0, $p->count());
        $p->replace($arr = ['bar' => 'bar']);
        $this->assertEquals(1, $p->count());
    }
}
