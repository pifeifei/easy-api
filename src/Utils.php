<?php

declare(strict_types=1);

namespace Pff\EasyApi;

use stdClass;

class Utils
{
    /**
     * 将布尔值转为字符串。
     *
     * @param array<string, mixed> $arr
     *
     * @return array<string, mixed>
     */
    public static function boolToString(array $arr): array
    {
        foreach ($arr as &$item) {
            if (\is_bool($item)) {
                $item = $item ? 'true' : 'false';

                continue;
            }

            if (\is_array($item)) {
                $item = static::boolToString($item);

                continue;
            }

            if ($item instanceof stdClass) {
                $item = static::boolToString((array) $item);
            }
        }
        unset($item);

        return $arr;
    }

    /**
     * 多维数组按键名排序。
     *
     * @param array<string, mixed> $arr
     *
     * @return array<string, mixed>
     */
    public static function ksortRecursive(array $arr): array
    {
        ksort($arr);
        foreach ($arr as &$item) {
            if (\is_scalar($item)) {
                continue;
            }
            if (\is_array($item)) {
                $item = static::ksortRecursive($item);

                continue;
            }

            if ($item instanceof stdClass) {
                $item = static::ksortRecursive((array) $item);
            }
        }
        unset($item);

        return $arr;
    }

    /**
     * 将数组中值为数组的转为 JSON 字符串。
     *
     * @param array<string, mixed> $arr
     * @param int $options JSON_xxx https://www.php.net/manual/en/json.constants.php
     *
     * @return array<string, bool|float|int|string>
     */
    public static function valueToJsonString(array $arr, int $options = 0): array
    {
        foreach ($arr as &$item) {
            if (\is_array($item)) {
                $item = json_encode($item, $options);
            }
        }
        unset($item);

        // @phpstan-ignore-next-line
        return $arr;
    }
}
