<?php

namespace Pff\EasyApi;

use stdClass;

class Utils
{
    /**
     * 将布尔值转为字符串
     *
     * @param array $arr
     *
     * @return array
     */
    public static function boolToString(array $arr): array
    {
        foreach ($arr as & $item) {

            if (is_bool($item)) {
                $item = $item ? 'true' : 'false';
            }

            if (is_array($item)) {
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
     * 多维数组按键名排序
     *
     * @param array $arr
     *
     * @return array
     */
    public static function ksortRecursive(array $arr): array
    {
        ksort($arr);
        foreach ($arr as & $item) {
            if (is_scalar($item)) {
                continue;
            }
            if (is_array($item)) {
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
     * 将数组中值为数组的转为 JSON 字符串
     *
     * @param array $arr
     * @param int $options
     *
     * @return array
     */
    public static function valueToJsonString(array $arr, int $options = 0): array
    {
        foreach ($arr as & $item) {
            if (is_array($item)) {
                $item = json_encode($item, $options);
            }
        }
        unset($item);

        return $arr;
    }
}
