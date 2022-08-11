<?php

declare(strict_types=1);

namespace Pff\EasyApi\Request;

use function implode;

use Pff\EasyApi\API;

use const PHP_OS;
use const PHP_VERSION;

class UserAgent
{
    /**
     * @var array<int|string, string>
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
     * @param array<int|string, string> $append
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
            if (is_numeric($key)) {
                $newUserAgent[] = $value;

                continue;
            }
            $newUserAgent[] = "{$key}/{$value}";
        }

        return $userAgent . implode(' ', $newUserAgent);
    }

    /**
     * @param array<int|string, string> $append
     *
     * @return array<int|string, string>
     */
    public static function clean(array $append): array
    {
        foreach ($append as $key => $value) {
            if (self::isGuarded($key)) {
                unset($append[$key]);
            }
        }

        return $append;
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
    public static function append(string $name, string $value = null): void
    {
        self::defaultFields();

        if (self::isGuarded($name)) {
            return;
        }

        if (null === $value) {
            self::$userAgent[] = $name;

            return;
        }

        self::$userAgent[$name] = $value;
    }

    /**
     * @param array<int|string, string> $userAgent
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
                'PHP' => PHP_VERSION,
            ];
        }
    }
}
