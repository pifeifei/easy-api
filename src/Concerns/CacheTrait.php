<?php

declare(strict_types=1);

namespace Pff\EasyApi\Concerns;

use Pff\EasyApi\Cache\Cache;
use Pff\EasyApi\Contracts\CacheInterface;
use Pff\EasyApi\Exception\ClientException;
use Psr\SimpleCache\CacheInterface as PsrCacheInterface;

trait CacheTrait
{
    /**
     * @var null|CacheInterface
     */
    protected $cache;

    public function cache(): CacheInterface
    {
        if (isset($this->cache)) {
            return $this->cache;
        }

        return $this->cache = new Cache();
    }

    /**
     * 设置缓存对象。
     *
     * @param class-string|PsrCacheInterface $cache
     *
     * @throws ClientException
     */
    public function setCache($cache): void
    {
        if ($cache instanceof PsrCacheInterface) {
            $this->cache()->setCache($cache);

            return;
        }

        if (!class_exists($cache)) {
            throw new ClientException(\sprintf('%s class does not exist.', $cache));
        }

        $obj = new $cache();
        if ($obj instanceof CacheInterface) {
            $this->cache = $obj;

            return;
        }

        throw new ClientException(\sprintf('Cache must implement %s interface.', CacheInterface::class));
    }
}
