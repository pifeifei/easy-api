<?php

namespace Pff\EasyApi\Request;

use Pff\EasyApi\API;

use function implode;

use const PHP_OS;
use const PHP_VERSION;

class UserAgent
{
    /**
     * @var array
     */
    private static $userAgent = [];

    /**
     * @var array
     */
    private static $guard = [
        'client',
        'php',
    ];

    /**
     * @param array $append
     *
     * @return string
     */
    public static function toString(array $append = [])
    {
        self::defaultFields();

        $os        = PHP_OS;
        $osVersion = php_uname('r');
        $osMode    = php_uname('m');
        $userAgent = "EasyApi ($os $osVersion; $osMode) ";

        $newUserAgent = [];

        $append = self::clean($append);

        $append = array_merge(self::$userAgent, $append);

        foreach ($append as $key => $value) {
            if ($value === null) {
                $newUserAgent[] = $key;
                continue;
            }
            if (is_numeric($key)) {
                $newUserAgent[] = $value;
                continue;
            }
            $newUserAgent[] = "$key/$value";
        }

        return $userAgent . implode(' ', $newUserAgent);
    }

    /**
     * UserAgent constructor.
     */
    private static function defaultFields()
    {
        if (self::$userAgent === []) {
            self::$userAgent = [
                'Client' => API::VERSION,
                'PHP'    => PHP_VERSION,
            ];
        }
    }

    /**
     * @param array $append
     *
     * @return array
     */
    public static function clean(array $append)
    {
        foreach ($append as $key => $value) {
            if (self::isGuarded($key)) {
                unset($append[$key]);
            }
        }

        return $append;
    }

    /**
     * @param $name
     *
     * @return bool
     */
    public static function isGuarded($name)
    {
        return in_array(strtolower($name), self::$guard, true);
    }

    /**
     * set User Agent of Alibaba Cloud.
     *
     * @param string $name
     * @param string $value
     *
     * @ throws ClientException
     */
    public static function append($name, $value = null)
    {
        self::defaultFields();

        if (!self::isGuarded($name)) {
            self::$userAgent[$name] = $value;
        }
    }

    /**
     * @param array $userAgent
     */
    public static function with(array $userAgent)
    {
        self::$userAgent = self::clean($userAgent);
    }

    /**
     * Clear all of the User Agent.
     */
    public static function clear()
    {
        self::$userAgent = [];
    }
}
