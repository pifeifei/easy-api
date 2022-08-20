<?php

declare(strict_types=1);

namespace Pff\EasyApiTest\Unit\Cache;

use Pff\EasyApi\Cache\Cache;
use Pff\EasyApi\Exception\ClientException;
use Pff\EasyApiTest\TestCase;
use Psr\SimpleCache\CacheInterface as PsrCacheInterface;

/**
 * @internal
 * @coversNothing
 */
final class CacheTest extends TestCase
{
    public function testCache(): void
    {
        $cache = new Cache();
        static::assertTrue($cache->set('foo', 'foo'));
        static::assertSame('foo', $cache->get('foo'));
        static::assertTrue($cache->has('foo'));
        static::assertNull($cache->get('none'));
        static::assertFalse($cache->has('none'));
    }

    public function testCacheClear(): void
    {
        $cache = new Cache();
        $cache->set('foo', 'bb');
        static::assertSame('bb', $cache->get('foo'));
        $cache->clear();
        static::assertNull($cache->get('foo'));
    }

    public function testException(): void
    {
        $this->expectException(ClientException::class);
        $this->expectExceptionMessage('API cache error');
         $this->expectExceptionCode(0);
        $cache = new Cache();
        $cache->has('xxx/s');
    }

    public function testInstance(): void
    {
        $cache = new Cache();
        static::assertInstanceOf(PsrCacheInterface::class, $cache->getDefaultCache());
        static::assertInstanceOf(PsrCacheInterface::class, $cache->getCache());
    }
}
