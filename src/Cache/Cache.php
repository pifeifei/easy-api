<?php

declare(strict_types=1);

namespace Pff\EasyApi\Cache;

use Pff\EasyApi\Contracts\CacheInterface;
use Pff\EasyApi\Exception\ClientException;
use Psr\SimpleCache\CacheInterface as PsrCacheInterface;
use Psr\SimpleCache\InvalidArgumentException;
use Symfony\Component\Cache\Adapter\FilesystemAdapter;
use Symfony\Component\Cache\Psr16Cache;

class Cache implements CacheInterface
{
    protected ?PsrCacheInterface $cache;

    protected string $namespace = 'easy-api';

    public function clear(): bool
    {
        return $this->getCache()->clear();
    }

    public function get(string $key, $defaultValue = null)
    {
        try {
            return $this->getCache()->get($key, $defaultValue);
        } catch (InvalidArgumentException $e) {
            throw new ClientException($this->ExceptionMessage($e), $this->ExceptionContext($key), $e->getCode(), $e);
        }
    }

    public function set(string $key, $value, $ttl = null): bool
    {
        try {
            return $this->getCache()->set($key, $value, $ttl);
        } catch (InvalidArgumentException $e) {
            throw new ClientException($this->ExceptionMessage($e), $this->ExceptionContext($key), $e->getCode(), $e);
        }
    }

    public function has(string $key): bool
    {
        try {
            return $this->getCache()->has($key);
        } catch (InvalidArgumentException $e) {
            throw new ClientException($this->ExceptionMessage($e), $this->ExceptionContext($key), $e->getCode(), $e);
        }
    }

    /**
     * 获取默认缓存对象。
     */
    public function getDefaultCache(): PsrCacheInterface
    {
        return new Psr16Cache(new FilesystemAdapter($this->namespace, 1500));
    }

    public function getCache(): PsrCacheInterface
    {
        if (isset($this->cache)) {
            return $this->cache;
        }

        return $this->cache = $this->getDefaultCache();
    }

    public function setCache(PsrCacheInterface $cache): void
    {
        $this->cache = $cache;
    }

    protected function ExceptionMessage(\Throwable $t): string
    {
        return \sprintf('API cache error: %s', $t->getMessage());
    }

    /**
     * @return array<string, mixed>
     */
    protected function ExceptionContext(string $key): array
    {
        return ['cacheKey' => $key];
    }
}
