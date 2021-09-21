<?php

namespace Pff\EasyApi\Concerns;

use ArrayIterator;
use Illuminate\Support\Arr;
use Illuminate\Support\Collection;

trait DataTrait
{
    /**
     * @var Collection
     */
    protected $collection;

    /**
     * @param mixed $value
     *
     * @return mixed|null
     */
    public function search($value)
    {
//        return JmesPath::search($expression, $this->collection->all());
        return $this->collection->search($value);
    }

    /**
     * Delete the contents of a given key or keys
     *
     * @param array|int|string|null $keys
     */
    public function clear($keys = null)
    {
        $this->delete($keys);
    }

    /**
     * Flatten an array with the given character as a key delimiter
     *
     * @param  string     $delimiter
     * @param  array|null $items
     * @param  string     $prepend
     * @return array
     */
    public function flatten($delimiter = '.', $items = null, $prepend = '')
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
        return Arr::get($this->collection->all(), $key, $default);
    }

    /**
     * Set a given key / value pair or pairs
     *
     * @param array|int|string $keys
     * @param mixed            $value
     */
    public function set($keys, $value = null)
    {
        $this->collection->put($keys, $value);
    }

    /**
     * Check if a given key or keys are empty
     *
     * @return bool
     */
    public function isEmpty()
    {
        return $this->collection->isEmpty();
    }

    /**
     * Return the value of a given key or all the values as JSON
     *
     * @param int   $options
     *
     * @return string
     */
    public function toJson($options = 0)
    {
        return $this->collection->toJson($options);
    }

    /**
     * @return array
     */
    public function toArray()
    {
        return $this->collection->all();
    }

    /**
     * Check if a given key exists
     *
     * @param int|string $key
     *
     * @return bool
     */
    public function offsetExists($key)
    {
        return $this->collection->has($key);
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
        return $this->collection->offsetGet($key);
    }

    /**
     * Set a given value to the given key
     *
     * @param int|string|null $key
     * @param mixed           $value
     */
    public function offsetSet($key, $value)
    {
        $this->collection->offsetSet($key, $value);
    }

    /**
     * Delete the given key
     *
     * @param int|string $key
     */
    public function offsetUnset($key)
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
        $this->collection->forget($keys);
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
    public function count()
    {
        return $this->collection->count();
    }

    /**
     * Get an iterator for the stored items
     *
     * @return ArrayIterator
     */
    public function getIterator()
    {
        return $this->collection->getIterator();
    }

    /**
     * Return items for JSON serialization
     *
     * @return array
     */
    public function jsonSerialize()
    {
        return $this->collection->jsonSerialize();
    }

    /**
     * @param string $name
     *
     * @return mixed|null
     */
    public function __get($name)
    {
        return $this->collection->get($name);
//        if (!isset($this->all()[$name])) {
//            return null;
//        }
//
//        return \json_decode(\json_encode($this->all()))->$name;
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
    public function all()
    {
        return $this->collection->all();
    }

    /**
     * @param string $name
     * @param mixed  $value
     */
    public function __set($name, $value)
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
            $this->collection = $this->collection->merge($this->collection);
        }

        if (! is_null($value)) {
            $this->collection->put($keys, $value);
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
    public function __isset($name)
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
    public function has($keys)
    {
        return $this->collection->has($keys);
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
        $this->collection = new Collection($data);
    }
}
