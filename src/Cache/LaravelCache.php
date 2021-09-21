<?php

namespace Pff\EasyApi\Cache;

use Illuminate\Support\Facades\Cache;
use Pff\EasyApi\Contracts\CacheInterface;

class LaravelCache implements CacheInterface
{
    /**
     * {@inheritdoc}
     */
    public function clear()
    {
        return Cache::tags(['easy', 'api'])->clear();
    }

    /**
     * {@inheritdoc}
     */
    public function get($key, $defaultValue = '')
    {
        return Cache::tags(['easy', 'api'])->get($key, $defaultValue);
    }

    /**
     * {@inheritdoc}
     */
    public function set($key, $value, $ttl = null)
    {
        return Cache::tags(['easy', 'api'])->put($key, $value, $ttl);
    }

    /**
     * {@inheritdoc}
     */
    public function has($key)
    {
        return Cache::tags(['easy', 'api'])->has($key);
    }
}
