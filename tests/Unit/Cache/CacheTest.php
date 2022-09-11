<?php

declare(strict_types=1);

namespace Pff\EasyApiTest\Unit\Cache;

use Pff\EasyApi\Cache\Cache;
use Pff\EasyApi\Exception\ClientException;
use Pff\EasyApiTest\TestCase;
use Psr\SimpleCache\CacheInterface as PsrCacheInterface;

/**
 * @internal
 *
 * @coversNothing
 */
final class CacheTest extends TestCase
{
    public function testCache(): void
    {
        $cache = new Cache();
        $this->assertTrue($cache->set('foo', 'foo'));
        $this->assertSame('foo', $cache->get('foo'));
        $this->assertTrue($cache->has('foo'));
        $this->assertNull($cache->get('none'));
        $this->assertFalse($cache->has('none'));
    }

    public function testCacheClear(): void
    {
        $cache = new Cache();
        $cache->set('foo', 'bb');
        $this->assertSame('bb', $cache->get('foo'));
        $cache->clear();
        $this->assertNull($cache->get('foo'));
    }

    public function testException(): void
    {
        $this->expectException(ClientException::class);
        $this->expectExceptionMessage('cache has');
        $this->expectExceptionCode(0);

        $cache = $this->createMock(Cache::class);
        $cache->expects($this->once())
            ->method('has')
            ->with('xxx')
            ->willThrowException(new ClientException('cache has exception.'))
        ;

        $cache->has('xxx');
    }

    public function testInstance(): void
    {
        $cache = new Cache();
        $this->assertInstanceOf(PsrCacheInterface::class, $cache->getDefaultCache());
        $this->assertInstanceOf(PsrCacheInterface::class, $cache->getCache());
    }
}
