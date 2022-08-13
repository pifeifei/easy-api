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
     * @var array<string, string[]|true>
     */
    protected $headers = [];

    /**
     * @var array<int|string, string[]|true>
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
            return $this->headers[strtr($key, self::UPPER, self::LOWER)] ?? [];
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
        /** @var string[] */
        return array_keys($this->all());
    }

    /**
     * Replaces the current HTTP headers by a new set.
     *
     * @param array<string, string|string[]> $headers
     */
    public function replace(array $headers = []): void
    {
        $this->headers = [];
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
     * @return null|string The first header value or default value
     */
    public function get(string $key, string $default = null): ?string
    {
        /** @var null[]|string[] $headers */
        $headers = $this->all($key);

        if (!$headers) {
            return $default;
        }

        if (null === $headers[0]) {
            return null;
        }

        return (string) $headers[0];
    }

    /**
     * Sets a header by name.
     *
     * @param bool|string|string[] $values The value or an array of values
     * @param bool $replace Whether to replace the actual value or not (true by default)
     */
    public function set(string $key, $values = true, bool $replace = true): void
    {
        $key = strtr($key, self::UPPER, self::LOWER);

        if (\is_array($values)) {
            $values = array_values($values);

            if (true === $replace || !isset($this->headers[$key])) {
                $this->headers[$key] = $values;
            } else {
                $this->headers[$key] = array_merge((array) $this->headers[$key], $values);
            }
        } else {
            if (true === $replace || !isset($this->headers[$key])) {
                $this->headers[$key] = \is_array($values) ? $values : [$values];
            } else {
                if (true === $values) {
                    $this->headers[$key] = true;
                } else {
                    $this->headers[$key][] = $values;
                }
            }
        }

        if ('cache-control' === $key) {
            $this->cacheControl = $this->parseCacheControl(implode(', ', $this->headers[$key]));
        }
    }

    /**
     * Returns true if the HTTP header is defined.
     *
     * @return bool true if the parameter exists, false otherwise
     */
    public function has(string $key): bool
    {
        return \array_key_exists(strtr($key, self::UPPER, self::LOWER), $this->all());
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
        $key = strtr($key, self::UPPER, self::LOWER);

        unset($this->headers[$key]);

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

        if (false === $date = DateTime::createFromFormat(DATE_RFC2822, $value)) {
            throw new RuntimeException(sprintf('The "%s" HTTP header is not parseable (%s).', $key, $value));
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
        $this->cacheControl[$key] = true === $value ? $value : [$value];

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
     * @param mixed $header
     *
     * @return array<string, string|true> An array representing the attribute values
     */
    protected function parseCacheControl($header): array
    {
        $parts = HeaderUtils::split($header, ',=');

        return HeaderUtils::combine($parts);
    }
}
