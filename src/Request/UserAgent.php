<?php

declare(strict_types=1);

namespace Pff\EasyApi\Request;

use Pff\EasyApi\API;

class UserAgent
{
    /**
     * @var array<string, string|true>
     */
    private static $userAgent = [];

    /**
     * @var array<string>
     */
    private static $guard = [
        'client',
        'php',
    ];

    /**
     * @param array<string, string|true> $append
     */
    public static function toString(array $append = []): string
    {
        self::defaultFields();

        $os = PHP_OS;
        $osVersion = php_uname('r');
        $osMode = php_uname('m');
        $userAgent = "EasyApi ({$os} {$osVersion}; {$osMode}) ";

        $newUserAgent = [];

        $append = self::clean($append);

        $append = array_merge(self::$userAgent, $append);

        foreach ($append as $key => $value) {
            if (true === $value) {
                $newUserAgent[] = ucfirst($key);

                continue;
            }
            $newUserAgent[] = ucfirst($key) . "/{$value}";
        }

        return $userAgent . implode(' ', $newUserAgent);
    }

    /**
     * 删除私有属性。
     *
     * @param array<int|string, string|true> $append
     *
     * @return array<string, string|true>
     */
    public static function clean(array $append): array
    {
        foreach ($append as $key => $value) {
            if (self::isGuarded($key)) {
                unset($append[$key]);
            }

            if (\is_int($key)) {
                $append[$value] = true;
                unset($append[$key]);
            }
        }

        return $append; // @phpstan-ignore-line
    }

    /**
     * @param int|string $name
     */
    public static function isGuarded($name): bool
    {
        if (\is_string($name)) {
            return \in_array(strtolower($name), self::$guard, true);
        }

        return false;
    }

    /**
     * set User Agent of Alibaba Cloud.
     *
     * @ throws ClientException
     */
    public static function append(string $name, ?string $value = null): void
    {
        self::defaultFields();

        if (self::isGuarded($name = strtolower($name))) {
            return;
        }

        if (null === $value) {
            self::$userAgent[$name] = true;

            return;
        }

        self::$userAgent[$name] = $value;
    }

    /**
     * @param array<int|string, string|true> $userAgent
     */
    public static function with(array $userAgent): void
    {
        self::$userAgent = self::clean($userAgent);
    }

    /**
     * Clear all user set userAgent.
     */
    public static function clear(): void
    {
        self::$userAgent = [];
    }

    /**
     * UserAgent constructor.
     */
    private static function defaultFields(): void
    {
        if ([] === self::$userAgent) {
            self::$userAgent = [
                'Client' => API::VERSION,
                'PHP' => PHP_MAJOR_VERSION . '.' . PHP_MINOR_VERSION,
            ];
        }
    }
}
