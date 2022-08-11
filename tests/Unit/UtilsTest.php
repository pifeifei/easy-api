<?php

declare(strict_types=1);

namespace Pff\EasyApiTest\Unit;

use Pff\EasyApi\Utils;
use Pff\EasyApiTest\TestCase;

/**
 * @internal
 * @coversNothing
 */
final class UtilsTest extends TestCase
{
    public function testBoolToString(): void
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
        static::assertSame($arr2, Utils::boolToString($arr1));
    }

    public function testValueToJsonString(): void
    {
        $arr1 = [
            'b' => 5,
            'a' => 10,
            'foo' => [
                'bar' => 'bar',
                'abc' => 123,
            ],
        ];
        $arr2 = [
            'b' => 5,
            'a' => 10,
            'foo' => '{"bar":"bar","abc":123}',
        ];
        static::assertSame($arr2, Utils::valueToJsonString($arr1));
    }

    public function testKSortRecursive(): void
    {
        $arr1 = [
            'b' => 5,
            'a' => 10,
            'foo' => [
                'bar' => 'bar',
                'abc' => 123,
            ],
            'bar' => json_decode('{"bar":"bar","abc":123}'),
        ];
        $arr2 = [
            'a' => 10,
            'b' => 5,
            'bar' => [
                'abc' => 123,
                'bar' => 'bar',
            ],
            'foo' => [
                'abc' => 123,
                'bar' => 'bar',
            ],
        ];
        static::assertSame($arr2, Utils::ksortRecursive($arr1));
    }
}
