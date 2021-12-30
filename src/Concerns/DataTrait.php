<?php

namespace Pff\EasyApi\Concerns;

use ArrayIterator;
use Illuminate\Support\Arr;

trait DataTrait
{
    /**
     * @var array
     */
    protected $collection;

    /**
     * Delete the contents of a given key or keys
     *
     * @param array|int|string|null $keys
     */
    public function clear($keys = null)
    {
        if (is_null($keys)) {
            $this->collection = [];
            return;
        }
        $this->delete($keys);
    }

    /**
     * Flatten an array with the given character as a key delimiter
     *
     * @param string $delimiter
     * @param array|null $items
     * @param string $prepend
     * @return array
     */
    public function flatten(string $delimiter = '.', array $items = null, string $prepend = ''): array
    {
        $flatten = [];

        if (is_null($items)) {
            $items = $this->collection;
        }

        foreach ($items as $key => $value) {
            if (is_array($value) && !empty($value)) {
                $flatten = array_merge(
                    $flatten,
                    $this->flatten($delimiter, $value, $prepend.$key.$delimiter)
                );
            } else {
                $flatten[$prepend.$key] = $value;
            }
        }

        return $flatten;
    }

    /**
     * Return the value of a given key
     *
     * @param int|string|null $key
     * @param mixed           $default
     *
     * @return mixed
     */
    public function get($key = null, $default = null)
    {
        return Arr::get($this->collection, $key, $default);
    }

    /**
     * Set a given key / value pair or pairs
     *
     * @param int|string $key  支持批量设置
     * @param mixed            $value
     */
    public function set($key, $value = null)
    {
        Arr::set($this->collection, $key, $value);
    }

    /**
     * Check if a given key or keys are empty
     *
     * @return bool
     */
    public function isEmpty(): bool
    {
        return empty($this->collection);
    }

    /**
     * Return the value of a given key or all the values as JSON
     *
     * @param int $options
     *
     * @return string
     */
    public function toJson(int $options = 0): string
    {
        return json_encode($this->collection, $options);
    }

    /**
     * @return array
     */
    public function toArray(): array
    {
        return $this->collection;
    }

    /**
     * Check if a given key exists
     *
     * @param int|string $key
     *
     * @return bool
     */
    public function offsetExists($key): bool
    {
        return Arr::has($this->collection, $key);
    }

    /**
     * Return the value of a given key
     *
     * @param int|string $key
     *
     * @return mixed
     */
    public function offsetGet($key)
    {
        return Arr::get($this->collection, $key);
    }

    /**
     * Set a given value to the given key
     *
     * @param int|string|null $key
     * @param mixed           $value
     */
    public function offsetSet($key, $value): void
    {
        $this->set($key, $value);
    }

    /**
     * Delete the given key
     *
     * @param int|string $key
     */
    public function offsetUnset($key): void
    {
        $this->delete($key);
    }

    /**
     * Delete the given key or keys
     *
     * @param array|int|string $keys
     */
    public function delete($keys)
    {
        Arr::forget($this->collection, $keys);
    }

    /*
     * --------------------------------------------------------------
     * ArrayAccess interface
     * --------------------------------------------------------------
     */

    /**
     * Return the number of items in a given key
     *
     * @return int
     */
    public function count(): int
    {
        return count($this->collection);
    }

    /**
     * Get an iterator for the stored items
     *
     * @return ArrayIterator
     */
    public function getIterator(): ArrayIterator
    {
        return new ArrayIterator($this->collection);
    }

    /**
     * Return items for JSON serialization
     *
     * @return array
     */
    public function jsonSerialize(): array
    {
        return $this->collection;
    }

    /**
     * @param string $name
     *
     * @return mixed|null
     */
    public function __get(string $name)
    {
        return $this->get($name);
    }

    /*
     * --------------------------------------------------------------
     * Countable interface
     * --------------------------------------------------------------
     */

    /**
     * Return all the stored items
     *
     * @return array
     */
    public function all(): array
    {
        return $this->collection;
    }

    /**
     * @param string $name
     * @param mixed  $value
     */
    public function __set(string $name, $value)
    {
        $this->add($name, $value);
    }

    /**
     * Set a given key / value pair or pairs
     * if the key doesn't exist already
     *
     * @param array|int|string $keys
     * @param mixed            $value
     */
    public function add($keys, $value = null)
    {
        if (is_array($keys)) {
            $arr = $this->flatten('.', $keys);
            foreach ($arr as $key => $item) {
                $this->set($key, $item);
            }
        }

        if (! is_null($value)) {
            Arr::set($this->collection, $keys, $value);
        }
    }


    /*
     * --------------------------------------------------------------
     * ObjectAccess
     * --------------------------------------------------------------
     */

    /**
     * @param string $name
     *
     * @return bool
     */
    public function __isset(string $name)
    {
        return $this->has($name);
    }

    /**
     * Check if a given key or keys exists
     *
     * @param array|int|string $keys
     *
     * @return bool
     */
    public function has($keys): bool
    {
        return Arr::has($this->collection, $keys);
    }

    /**
     * @param $name
     *
     * @return void
     */
    public function __unset($name)
    {
        $this->delete($name);
    }

    protected function collection($data = [])
    {
        $this->collection = $data;
    }
}
