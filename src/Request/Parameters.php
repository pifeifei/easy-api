<?php

namespace Pff\EasyApi\Request;


use ArrayIterator;
use Countable;
use Illuminate\Support\Arr;
use IteratorAggregate;

use function count;

class Parameters implements IteratorAggregate, Countable
{
    /**
     * Parameter storage.
     */
    protected $parameters;

    public function __construct(array $parameters = [])
    {
        $this->parameters = $parameters;
    }

    /**
     * Returns the parameters.
     *
     * @return array An int, float, boolean, string or array of parameters
     */
    public function all(): array
    {
        return $this->parameters;
    }

    /**
     * Returns the parameter keys.
     *
     * @return array An array of parameter keys
     */
    public function keys(): array
    {
        return array_keys($this->parameters);
    }

    /**
     * Replaces the current parameters by a new set.
     *
     * @param array $parameters
     * @return Parameters
     */
    public function replace(array $parameters = []): Parameters
    {
        $this->parameters = $parameters;
        return $this;
    }

    /**
     * Adds parameters.
     *
     * @param array $parameters
     * @return Parameters
     */
    public function add(array $parameters = []): Parameters
    {
        $this->parameters = array_replace($this->parameters, $parameters);

        return $this;
    }

    /**
     * Returns a parameter by name.
     *
     * @param mixed|null $default The default value if the parameter key does not exist
     *
     * @return mixed
     */
    public function get($key, $default = null)
    {
        return Arr::get($this->parameters, $key, $default);
    }

    /**
     * Sets a parameter by name.
     *
     * @param string|null $key
     * @param mixed $value The value
     * @return Parameters
     */
    public function set(?string $key, $value): Parameters
    {
        Arr::set($this->parameters, $key, $value);
        return $this;
    }

    /**
     * Returns true if the parameter is defined.
     *
     * @param string $key
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
     * @param string|array $keys
     * @return Parameters
     */
    public function remove($keys): Parameters
    {
        Arr::forget($this->parameters, $keys);
        return $this;
    }

    /**
     * Returns an iterator for parameters.
     *
     * @return ArrayIterator An \ArrayIterator instance
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
        return count($this->parameters);
    }
}
