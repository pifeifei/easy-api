<?php

namespace Pff\EasyApi\Cache;

use Illuminate\Support\Facades\Cache;
use Pff\EasyApi\Contracts\CacheInterface;

class LaravelCache implements CacheInterface
{
    /**
     * {@inheritdoc}
     */
    public function clear(): bool
    {
        return Cache::tags(['easy', 'api'])->clear();
    }

    /**
     * {@inheritdoc}
     */
    public function get(string $key, $defaultValue = '')
    {
        return Cache::tags(['easy', 'api'])->get($key, $defaultValue);
    }

    /**
     * {@inheritdoc}
     */
    public function set(string $key, $value, $ttl = null): bool
    {
        return Cache::tags(['easy', 'api'])->put($key, $value, $ttl);
    }

    /**
     * {@inheritdoc}
     */
    public function has(string $key): bool
    {
        return Cache::tags(['easy', 'api'])->has($key);
    }
}
