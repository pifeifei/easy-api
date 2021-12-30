<?php

namespace Pff\EasyApi\Cache;

use Pff\EasyApi\Contracts\CacheInterface;
use Symfony\Component\Cache\Adapter\FilesystemAdapter;
use Symfony\Component\Cache\Psr16Cache;

class Cache implements CacheInterface
{
    protected $cache;

    /**
     * {@inheritdoc}
     */
    public function clear(): bool
    {
        return $this->getCache()->clear();
    }

    /**
     * 获取缓存对象
     *
     * 如果是 linux 系统，请设置缓存目录， /tmp 空间很小的，容易出错。
     *
     * @return Psr16Cache
     */
    protected function getCache(): Psr16Cache
    {
        return new Psr16Cache(new FilesystemAdapter('easy-api', 1500));
    }

    /**
     * {@inheritdoc}
     */
    public function get(string $key, $defaultValue = '')
    {
        return $this->getCache()->get($key, $defaultValue);
    }

    /**
     * {@inheritdoc}
     */
    public function set(string $key, $value, $ttl = null): bool
    {
        return $this->getCache()->set($key, $value, $ttl);
    }

    /**
     * {@inheritdoc}
     */
    public function has(string $key): bool
    {
        return $this->getCache()->has($key);
    }
}
