<?php

declare(strict_types=1);

namespace Pff\EasyApi\Concerns;

use ArrayIterator;
use Illuminate\Support\Arr;
use Pff\EasyApi\Exception\ClientException;

trait DataTrait
{
    /** @var array<string, mixed> */
    protected array $collection;

    /** @return null|mixed */
    public function __get(string $name)
    {
        return $this->get($name);
    }

    /**
     * @param mixed $value
     */
    public function __set(string $name, $value): void
    {
        $this->add($name, $value);
    }

    /*
     * --------------------------------------------------------------
     * ObjectAccess
     * --------------------------------------------------------------
     */

    /**
     * @return bool
     */
    public function __isset(string $name)
    {
        return $this->has($name);
    }

    /**
     * @param string $name
     */
    public function __unset($name): void
    {
        $this->delete($name);
    }

    /**
     * Delete the contents of a given key or keys.
     *
     * @param string|string[] $keys
     */
    public function clear($keys = null): void
    {
        if (null === $keys) {
            $this->collection = [];

            return;
        }
        $this->delete($keys);
    }

    /**
     * Flatten an array with the given character as a key delimiter.
     *
     * @param array<string, mixed> $items 可以是多维数组
     *
     * @return array<string, mixed> 一维数组，键名为点分字符串
     */
    public function flatten(string $delimiter = '.', array $items = null, string $prepend = ''): array
    {
        $flatten = [];

        if (null === $items) {
            $items = $this->collection;
        }

        foreach ($items as $key => $value) {
            if (\is_array($value) && !empty($value)) {
                /** @phpstan-ignore-next-line */
                $flatten = array_merge($flatten, $this->flatten($delimiter, $value, $prepend . $key . $delimiter));
            } else {
                $flatten[$prepend . $key] = $value;
            }
        }

        return $flatten;
    }

    /**
     * Return the value of a given key.
     *
     * @param null|int|string $key
     * @param mixed $default
     *
     * @return mixed
     */
    public function get($key = null, $default = null)
    {
        return Arr::get($this->collection, $key, $default);
    }

    /**
     * Set a given key / value pair or pairs.
     *
     * @param string $key 支持批量设置
     * @param mixed $value
     */
    public function set(string $key, $value = null): void
    {
        Arr::set($this->collection, $key, $value);
    }

    /**
     * Check if a given key or keys are empty.
     */
    public function isEmpty(): bool
    {
        return empty($this->collection);
    }

    /**
     * Return the value of a given key or all the values as JSON.
     *
     * @throws ClientException
     */
    public function toJson(int $options = 0): string
    {
        $result = json_encode($this->collection, $options);
        if (false === $result) {
            throw new ClientException('相应数据解析错误。', ['data' => $this->collection]);
        }

        return $result;
    }

    /**
     * @return array<string, mixed>
     */
    public function toArray(): array
    {
        return $this->collection;
    }

    /**
     * Check if a given key exists.
     *
     * @param string $key
     */
    public function offsetExists($key): bool
    {
        return Arr::has($this->collection, $key);
    }

    /**
     * Return the value of a given key.
     *
     * @param int|string $key
     *
     * @return mixed
     */
    #[\ReturnTypeWillChange]
    public function offsetGet($key)
    {
        return Arr::get($this->collection, $key);
    }

    /**
     * Set a given value to the given key.
     *
     * @param string $key
     * @param mixed $value
     */
    public function offsetSet($key, $value): void
    {
        $this->set($key, $value);
    }

    /**
     * Delete the given key.
     *
     * @param string $key
     */
    public function offsetUnset($key): void
    {
        $this->delete($key);
    }

    /**
     * Delete the given key or keys.
     *
     * @param string|string[] $keys
     */
    public function delete($keys): void
    {
        Arr::forget($this->collection, $keys);
    }

    /*
     * --------------------------------------------------------------
     * ArrayAccess interface
     * --------------------------------------------------------------
     */

    /**
     * Return the number of items in a given key.
     */
    public function count(): int
    {
        return \count($this->collection);
    }

    /**
     * Get an iterator for the stored items.
     */
    public function getIterator(): ArrayIterator
    {
        return new ArrayIterator($this->collection);
    }

    /**
     * Return items for JSON serialization.
     *
     * @return array<string, mixed>
     */
    public function jsonSerialize(): array
    {
        return $this->collection;
    }

    /*
     * --------------------------------------------------------------
     * Countable interface
     * --------------------------------------------------------------
     */

    /**
     * Return all the stored items.
     *
     * @return array<string, mixed>
     */
    public function all(): array
    {
        return $this->collection;
    }

    /**
     * Set a given key / value pair or pairs
     * if the key doesn't exist already.
     *
     * @param array<string, mixed>|string $keys
     * @param mixed $value
     */
    public function add($keys, $value = null): void
    {
        if (\is_array($keys)) {
            $arr = $this->flatten('.', $keys);
            foreach ($arr as $key => $item) {
                $this->set($key, $item);
            }

            return;
        }

        if (null !== $value) {
            Arr::set($this->collection, $keys, $value);

            return;
        }

        if (null === $value) {
            Arr::forget($this->collection, $keys);
        }
    }

    /**
     * Check if a given key or keys exists.
     */
    public function has(string $key): bool
    {
        return Arr::has($this->collection, $key);
    }

    /**
     * @param array<string, mixed> $data
     */
    protected function collection(array $data = []): void
    {
        $this->collection = $data;
    }
}
