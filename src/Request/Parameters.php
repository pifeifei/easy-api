<?php

declare(strict_types=1);

namespace Pff\EasyApi\Request;

use ArrayIterator;
use Countable;
use Illuminate\Support\Arr;
use IteratorAggregate;

/**
 * 参数。
 *
 * @template TKey of string
 * @template TValue of mixed
 *
 * @implements IteratorAggregate<TKey, TValue>
 */
class Parameters implements IteratorAggregate, Countable
{
    /**
     * Parameter storage.
     *
     * @var array<TKey, TValue>
     */
    protected array $parameters;

    /**
     * @param array<TKey, TValue> $parameters
     */
    public function __construct(array $parameters = [])
    {
        $this->parameters = $parameters;
    }

    /**
     * Returns the parameters.
     *
     * @return array<TKey, TValue> An int, float, boolean, string or array of parameters
     */
    public function all(): array
    {
        return $this->parameters;
    }

    public function clean(): self
    {
        $this->parameters = [];
        return $this;
    }

    /**
     * Returns the parameter keys.
     *
     * @return string[] An array of parameter keys
     */
    public function keys(): array
    {
        return array_keys($this->parameters);
    }

    /**
     * Replaces the current parameters by a new set.
     *
     * @param array<TKey, TValue> $parameters
     */
    public function replace(array $parameters = []): self
    {
        $this->parameters = $parameters;

        return $this;
    }

    /**
     * Adds parameters.
     *
     * @param array<TKey, TValue> $parameters
     */
    public function add(array $parameters = []): self
    {
        $this->parameters = array_replace($this->parameters, $parameters);

        return $this;
    }

    /**
     * Returns a parameter by name.
     *
     * @param null|TValue $default The default value if the parameter key does not exist
     * @param TValue $key
     *
     * @return TValue
     */
    public function get($key, $default = null)
    {
        return Arr::get($this->parameters, $key, $default); // @phpstan-ignore-line
    }

    /**
     * Sets a parameter by name.
     *
     * @param mixed $value The value
     */
    public function set(string $key, $value): self
    {
        Arr::set($this->parameters, $key, $value);

        return $this;
    }

    /**
     * Returns true if the parameter is defined.
     *
     * @return bool true if the parameter exists, false otherwise
     */
    public function has(string $key): bool
    {
        return Arr::has($this->parameters, $key);
    }

    /**
     * Remove one or many array items from a given array using "dot" notation.
     *
     * @param array<string>|string $keys
     */
    public function remove($keys): self
    {
        Arr::forget($this->parameters, $keys);

        return $this;
    }

    /**
     * Returns an iterator for parameters.
     *
     * @return ArrayIterator<TKey, TValue> An \ArrayIterator instance
     */
    public function getIterator(): ArrayIterator
    {
        return new ArrayIterator($this->parameters);
    }

    /**
     * Returns the number of parameters.
     *
     * @return int The number of parameters
     */
    public function count(): int
    {
        return \count($this->parameters);
    }

    public function isEmpty(): bool
    {
        return empty($this->parameters);
    }

    public function isNotEmpty(): bool
    {
        return !$this->isEmpty();
    }
}
