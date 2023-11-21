<?php

declare(strict_types=1);

namespace Pff\EasyApi;

use Pff\EasyApi\Exception\InvalidArgumentException;

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
                $item = static::boolToString($item); // @phpstan-ignore-line

                continue;
            }

            if ($item instanceof \stdClass) {
                $item = static::boolToString((array) $item);
            }
        }
        unset($item);

        return $arr;
    }

    /**
     * 发生错误时抛出的 json_decode 包装器异常。
     *
     * @param string $json JSON data to parse
     * @param bool $assoc when true, returned objects will be converted
     *                    into associative arrays
     * @param int $options bitmask of JSON decode options
     * @param int<1, max> $depth user specified recursion depth
     *
     * @throws InvalidArgumentException if the JSON cannot be decoded
     *
     * @return array<string, mixed>
     *
     * @see https://www.php.net/manual/en/function.json-decode.php
     */
    public static function jsonDecode(string $json, bool $assoc = true, int $options = 0, int $depth = 512): array
    {
        $data = json_decode($json, $assoc, $depth, $options);
        if (JSON_ERROR_NONE !== json_last_error()) {
            throw new InvalidArgumentException('json_decode error: ' . json_last_error_msg(), ['source' => $json]);
        }

        if (\is_array($data)) {
            return $data; // @phpstan-ignore-line
        }

        throw new InvalidArgumentException('json_decode error: must be json string of array.', ['source' => $json]);
    }

    /**
     * Wrapper for JSON encoding that throws when an error occurs.
     *
     * @param mixed $value The value being encoded
     * @param int $options JSON encode option bitmask
     * @param int<1, max> $depth Set the maximum depth. Must be greater than zero.
     *
     * @throws InvalidArgumentException if the JSON cannot be encoded
     *
     * @see https://www.php.net/manual/en/function.json-encode.php
     */
    public static function jsonEncode($value, int $options = JSON_THROW_ON_ERROR, int $depth = 512): string
    {
        $json = json_encode($value, $options, $depth);
        if (JSON_ERROR_NONE !== json_last_error()) {
            throw new InvalidArgumentException(
                'json_encode error: ' . json_last_error_msg(),
                ['source' => $value, 'option' => $options]
            );
        }

        return $json;
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
                $item = static::ksortRecursive($item); // @phpstan-ignore-line

                continue;
            }

            if ($item instanceof \stdClass) {
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
