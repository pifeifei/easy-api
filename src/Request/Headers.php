<?php

declare(strict_types=1);

namespace Pff\EasyApi\Request;

use ArrayIterator;
use Countable;

use const DATE_RFC2822;

use DateTime;
use DateTimeInterface;
use IteratorAggregate;
use RuntimeException;

class Headers implements IteratorAggregate, Countable
{
    public const UPPER = '_ABCDEFGHIJKLMNOPQRSTUVWXYZ';
    public const LOWER = '-abcdefghijklmnopqrstuvwxyz';

    /**
     * @var array<string, string[]>
     * @ var array<string, array<string, string>|array<string, true>|string|true>
     */
    protected $headers = [];

    /**
     * @var array<string, string>
     */
    protected $headerNames = [];

    /**
     * @var array<string, string|true>
     *
     * @see https://developer.mozilla.org/zh-CN/docs/Web/HTTP/Headers/Cache-Control
     */
    protected $cacheControl = [];

    /**
     * @param array<string, string|string[]> $headers
     */
    public function __construct(array $headers = [])
    {
        foreach ($headers as $key => $values) {
            $this->set($key, $values);
        }
    }

    /**
     * Returns the headers.
     *
     * @param null|string $key The name of the headers to return or null to get them all
     *
     * @return array<string|string[]> An array of headers
     */
    public function all(string $key = null): array
    {
        if (null !== $key) {
            $headerName = strtr($key, self::UPPER, self::LOWER);
            $this->headerNames[$headerName] = $key;

            return $this->headers[$key] ?? [];
        }

        return $this->headers;
    }

    /**
     * Returns the parameter keys.
     *
     * @return string[] An array of parameter keys
     */
    public function keys(): array
    {
//        /** @var string[] */
//        return array_keys($this->all());
        return $this->headerNames;
    }

    /**
     * Replaces the current HTTP headers by a new set.
     *
     * @param array<string, string|string[]> $headers
     */
    public function replace(array $headers = []): void
    {
        $this->headers = [];
        $this->headerNames = [];
        $this->add($headers);
    }

    /**
     * Adds new headers the current HTTP headers set.
     *
     * @param array<string, string|string[]> $headers
     */
    public function add(array $headers): void
    {
        foreach ($headers as $key => $values) {
            $this->set($key, $values);
        }
    }

    /**
     * Returns a header value by name.
     *
     * @return string[] The first header value or default value
     */
    public function get(string $header): array
    {
        $header = strtolower($header);

        if (!isset($this->headerNames[$header])) {
            return [];
        }

        $header = $this->headerNames[$header];

        return $this->headers[$header];
    }

    public function getLine(string $header): ?string
    {
        $value = $this->get($header);
        if (empty($value)) {
            return '';
        }

        $new = [];
        foreach ($value as $k => $v) {
            if (is_numeric($k)) {
                $new[] = $v;
            } else {
                $new[] = sprintf('%s: %s', $k, $v);
            }
        }

        return implode(', ', $new);
    }

    /**
     * Sets a header by name.
     *
     * @param string|string[] $values The value or an array of values
     * @param bool $replace Whether to replace the actual value or not (true by default)
     */
    public function set(string $key, $values, bool $replace = true): void
    {
        $headerName = strtr($key, self::UPPER, self::LOWER);
        $values = \is_string($values) ? [$values] : array_values($values);

        if (true === $replace || !isset($this->headers[$key])) {
            $this->headers[$key] = $values;
        } else {
            $this->headers[$key] = array_merge($this->headers[$key], $values);
        }

        if ('cache-control' === $headerName) {
            $this->cacheControl = $this->parseCacheControl(implode(', ', $this->headers[$key]));
        }

        $this->headerNames[$headerName] = $key;
    }

    /**
     * Returns true if the HTTP header is defined.
     *
     * @return bool true if the parameter exists, false otherwise
     */
    public function has(string $key): bool
    {
        return \array_key_exists(strtr($key, self::UPPER, self::LOWER), $this->keys());
    }

    /**
     * Returns true if the given HTTP header contains the given value.
     *
     * @return bool true if the value is contained in the header, false otherwise
     */
    public function contains(string $key, string $value): bool
    {
        return \in_array($value, $this->all($key), true);
    }

    /**
     * Removes a header.
     */
    public function remove(string $key): void
    {
        $headerName = strtr($key, self::UPPER, self::LOWER);

        unset($this->headers[$key], $this->headerNames[$headerName]);

        if ('cache-control' === $key) {
            $this->cacheControl = [];
        }
    }

    /**
     * Returns the HTTP header value converted to a date.
     *
     * @throws RuntimeException When the HTTP header is not parseable
     *
     * @return null|DateTimeInterface The parsed DateTime or the default value if the header does not exist
     */
    public function getDate(string $key, DateTime $default = null)
    {
        if (null === $value = $this->get($key)) {
            return $default;
        }

        if (false === $date = DateTime::createFromFormat(DATE_RFC2822, $value[0])) {
            throw new RuntimeException(sprintf('The "%s" HTTP header is not parseable (%s).', $key, $value[0]));
        }

        return $date;
    }

    /**
     * Adds a custom Cache-Control directive.
     *
     * @param string|true $value
     */
    public function addCacheControlDirective(string $key, $value = true): void
    {
        $this->cacheControl[$key] = $value;

        $this->set('Cache-Control', $this->getCacheControlHeader());
    }

    /**
     * Returns true if the Cache-Control directive is defined.
     *
     * @return bool true if the directive exists, false otherwise
     */
    public function hasCacheControlDirective(string $key): bool
    {
        return \array_key_exists($key, $this->cacheControl);
    }

    /**
     * Returns a Cache-Control directive value by name.
     *
     * @return null|bool|string The directive value if defined, null otherwise
     */
    public function getCacheControlDirective(string $key)
    {
        return $this->cacheControl[$key] ?? null;
    }

    /**
     * Removes a Cache-Control directive.
     *
     * @param mixed $key
     */
    public function removeCacheControlDirective($key): void
    {
        unset($this->cacheControl[$key]);

        $this->set('Cache-Control', $this->getCacheControlHeader());
    }

    /**
     * Returns an iterator for headers.
     *
     * @return ArrayIterator An \ArrayIterator instance
     */
    public function getIterator(): ArrayIterator
    {
        return new ArrayIterator($this->headers);
    }

    /**
     * Returns the number of headers.
     *
     * @return int The number of headers
     */
    public function count(): int
    {
        return \count($this->headers);
    }

    protected function getCacheControlHeader(): string
    {
        ksort($this->cacheControl);

        return HeaderUtils::toString($this->cacheControl, ',');
    }

    /**
     * Parses a Cache-Control HTTP header.
     *
     * @return array<string, string|true> An array representing the attribute values
     */
    protected function parseCacheControl(string $header): array
    {
        /** @var string[][] $parts */
        $parts = HeaderUtils::split($header, ',=');

        return HeaderUtils::combine($parts);
    }
}
