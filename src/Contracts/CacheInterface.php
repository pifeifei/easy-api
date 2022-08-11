<?php

declare(strict_types=1);

namespace Pff\EasyApi\Contracts;

use Pff\EasyApi\Exception\ClientException;
use Psr\SimpleCache\CacheInterface as PsrCacheInterface;

interface CacheInterface
{
    /**
     * Wipes clean the entire cache's keys.
     *
     * @return bool true on success and false on failure
     */
    public function clear(): bool;

    /**
     * Fetches a value from the cache.
     *
     * @param string $key the unique key of this item in the cache
     * @param mixed $defaultValue default value to return if the key does not exist
     *
     * @throws ClientException MUST be thrown if the $key string is not a legal value
     *
     * @return mixed
     */
    public function get(string $key, $defaultValue = null);

    /**
     * @param string $key the cache item key
     *
     * @throws ClientException MUST be thrown if the $key string is not a legal value
     */
    public function has(string $key): bool;

    /**
     * Persists data in the cache, uniquely referenced by a key with an optional expiration TTL time.
     *
     * @param string $key the key of the item to store
     * @param mixed $value the value of the item to store, must be serializable
     * @param null|\DateInterval|int $ttl Optional. The TTL value of this item. If no value is sent and
     *                                    the driver supports TTL then the library may set a default value
     *                                    for it or let the driver take care of that.
     *
     * @throws ClientException MUST be thrown if the $key string is not a legal value
     *
     * @return bool true on success and false on failure
     */
    public function set(string $key, $value, $ttl = null): bool;

    /**
     * 获取缓存对象.
     *
     * 如果是 linux 系统，请设置缓存目录， /tmp 空间很小的，容易出错。
     */
    public function getCache(): PsrCacheInterface;

    /**
     * 设置缓存对象。
     *
     * @param PsrCacheInterface $cache
     */
    public function setCache(PsrCacheInterface $cache): void;
}
