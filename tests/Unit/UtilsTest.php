<?php

namespace Pff\EasyApiTest\Unit;

use Pff\EasyApi\Utils;
use Pff\EasyApiTest\TestCase;

class UtilsTest extends TestCase
{
    public function testBoolToString()
    {
        $arr1 = [
            'foo' => true,
            'bar' => false,
            'foobar' => 'foobar',
            'int' => 123,
        ];
        $arr2 = [
            'foo' => 'true',
            'bar' => 'false',
            'foobar' => 'foobar',
            'int' => 123,
        ];
        $this->assertEquals($arr2, Utils::boolToString($arr1));
    }


    public function testValueToJsonString()
    {
        $arr1 = [
            'b' => 5,
            'a' => 10,
            'foo' => [
                'bar' => 'bar',
                'abc' => 123
            ]
        ];
        $arr2 = [
            'a' => 10,
            'b' => 5,
            'foo' => '{"bar":"bar","abc":123}'
        ];
        $this->assertEquals($arr2, Utils::valueToJsonString($arr1));
    }


    public function testKSortRecursive()
    {
        $arr = [
            'b' => 5,
            'a' => 10,
            'foo' => [
                'bar' => 'bar',
                'abc' => 123
            ],
            'bar' => json_decode('{"bar":"bar","abc":123}')
        ];
        $arr2 = [
            'a' => 10,
            'b' => 5,
            'foo' => [
                'abc' => 123,
                'bar' => 'bar',
            ],
            'bar' => [
                'abc' => 123,
                'bar' => 'bar',
            ]
        ];
        $this->assertEquals($arr2, Utils::ksortRecursive($arr));
    }
}
