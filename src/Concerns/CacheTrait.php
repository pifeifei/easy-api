<?php

namespace Pff\EasyApi\Concerns;

use Pff\EasyApi\Contracts\CacheInterface;
use Pff\EasyApi\Exception\ClientException;

trait CacheTrait
{

    /**
     * @var CacheInterface
     */
    protected $cache;

    /**
     * @return CacheInterface
     */
    public function cache(): CacheInterface
    {
        return $this->cache;
    }

    /**
     * @param CacheInterface|string|null $cache
     * @return CacheTrait
     */
    public function setCache($cache)
    {
        if (is_null($cache)) {
            return $this;
        }
        if (is_string($cache) && class_exists($cache)) {
            $cache = new $cache();
        }

        if ($cache instanceof CacheInterface) {
            $this->cache = $cache;
            return $this;
        }

        throw new \UnexpectedValueException(sprintf('Cache must implement %s interface.', CacheInterface::class));
    }
}
