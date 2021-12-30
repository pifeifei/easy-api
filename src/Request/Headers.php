<?php

namespace Pff\EasyApi\Request;

use ArrayIterator;
use Countable;
use DateTime;
use DateTimeInterface;
use IteratorAggregate;
use RuntimeException;

use function array_key_exists;
use function count;
use function in_array;
use function is_array;

use const DATE_RFC2822;

class Headers implements IteratorAggregate, Countable
{
    const UPPER = '_ABCDEFGHIJKLMNOPQRSTUVWXYZ';
    const LOWER = '-abcdefghijklmnopqrstuvwxyz';

    protected $headers = [];
    protected $cacheControl = [];

    public function __construct(array $headers = [])
    {
        foreach ($headers as $key => $values) {
            $this->set($key, $values);
        }
    }

    /**
     * Returns the headers.
     *
     * @param string|null $key The name of the headers to return or null to get them all
     *
     * @return array An array of headers
     */
    public function all(string $key = null): array
    {
        if (null !== $key) {
            $key2 = strtr($key, self::UPPER, self::LOWER);
            return $this->headers[$key2] ?? [];
        }

        return $this->headers;
    }

    /**
     * Returns the parameter keys.
     *
     * @return array An array of parameter keys
     */
    public function keys(): array
    {
        return array_keys($this->all());
    }

    /**
     * Replaces the current HTTP headers by a new set.
     */
    public function replace(array $headers = [])
    {
        $this->headers = [];
        $this->add($headers);
    }

    /**
     * Adds new headers the current HTTP headers set.
     */
    public function add(array $headers)
    {
        foreach ($headers as $key => $values) {
            $this->set($key, $values);
        }
    }

    /**
     * Returns a header value by name.
     *
     * @return string|null The first header value or default value
     */
    public function get($key, $default = null)
    {
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
     * @param string|string[]|null $values  The value or an array of values
     * @param bool $replace Whether to replace the actual value or not (true by default)
     */
    public function set($key, $values, bool $replace = true)
    {
        $key = strtr($key, self::UPPER, self::LOWER);

        if (is_array($values)) {
            $values = array_values($values);

            if (true === $replace || !isset($this->headers[$key])) {
                $this->headers[$key] = $values;
            } else {
                $this->headers[$key] = array_merge($this->headers[$key], $values);
            }
        } else {
            if (true === $replace || !isset($this->headers[$key])) {
                $this->headers[$key] = [$values];
            } else {
                $this->headers[$key][] = $values;
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
    public function has($key): bool
    {
        return array_key_exists(strtr($key, self::UPPER, self::LOWER), $this->all());
    }

    /**
     * Returns true if the given HTTP header contains the given value.
     *
     * @return bool true if the value is contained in the header, false otherwise
     */
    public function contains($key, $value): bool
    {
        return in_array($value, $this->all($key));
    }

    /**
     * Removes a header.
     */
    public function remove($key)
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
     * @return DateTimeInterface|null The parsed DateTime or the default value if the header does not exist
     *
     * @throws RuntimeException When the HTTP header is not parseable
     */
    public function getDate($key, DateTime $default = null)
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
     * @param bool|string $value The Cache-Control directive value
     */
    public function addCacheControlDirective($key, $value = true)
    {
        $this->cacheControl[$key] = $value;

        $this->set('Cache-Control', $this->getCacheControlHeader());
    }

    /**
     * Returns true if the Cache-Control directive is defined.
     *
     * @return bool true if the directive exists, false otherwise
     */
    public function hasCacheControlDirective($key): bool
    {
        return array_key_exists($key, $this->cacheControl);
    }

    /**
     * Returns a Cache-Control directive value by name.
     *
     * @return bool|string|null The directive value if defined, null otherwise
     */
    public function getCacheControlDirective($key)
    {
        return $this->cacheControl[$key] ?? null;
    }

    /**
     * Removes a Cache-Control directive.
     */
    public function removeCacheControlDirective($key)
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
        return count($this->headers);
    }

    /**
     * @return string
     */
    protected function getCacheControlHeader(): string
    {
        ksort($this->cacheControl);

        return HeaderUtils::toString($this->cacheControl, ',');
    }

    /**
     * Parses a Cache-Control HTTP header.
     *
     * @return array An array representing the attribute values
     */
    protected function parseCacheControl($header): array
    {
        $parts = HeaderUtils::split($header, ',=');

        return HeaderUtils::combine($parts);
    }
}
