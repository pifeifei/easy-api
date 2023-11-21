<?php

declare(strict_types=1);

namespace Pff\EasyApiTest\Unit\Request;

use Pff\EasyApi\Request\Headers;
use Pff\EasyApiTest\TestCase;

/**
 * @internal
 *
 * @coversNothing
 */
final class HeadersTest extends TestCase
{
    public function testConstructor(): void
    {
        $bag = new Headers(['foo' => 'bar']);
        $this->assertTrue($bag->has('foo'));
    }

    public function testKeys(): void
    {
        $bag = new Headers(['Foo' => 'bar']);
        $keys = $bag->keys();
        $this->assertSame('Foo', $keys['foo']);
    }

    public function testGetDate(): void
    {
        $bag = new Headers(['foo' => 'Tue, 4 Sep 2012 20:00:00 +0200']);
        $headerDate = $bag->getDate('foo');
        $this->assertInstanceOf(\DateTime::class, $headerDate);
    }

    public function testGetDateNull(): void
    {
        $bag = new Headers(['foo' => (new \DateTime())->format(DATE_RFC2822)]);
        $headerDate = $bag->getDate('foo');
        $this->assertInstanceOf(\DateTime::class, $headerDate);
    }

    public function testGetDateException(): void
    {
        $this->expectException(\RuntimeException::class);
        $bag = new Headers(['foo' => 'Tue']);
        $bag->getDate('foo');
    }

    public function testGetCacheControlHeader(): void
    {
        $bag = new Headers();
        $bag->addCacheControlDirective('public', '#a');
        $this->assertTrue($bag->hasCacheControlDirective('public'));
        $this->assertSame('#a', $bag->getCacheControlDirective('public'));
    }

    public function testAll(): void
    {
        $bag = new Headers(['foo' => 'bar']);
        $this->assertSame(['foo' => ['bar']], $bag->all());

        $bag = new Headers(['FOO' => 'BAR']);
        $this->assertSame(['FOO' => ['BAR']], $bag->all());
    }

    public function testReplace(): void
    {
        $bag = new Headers(['Foo' => 'bar']);
        $bag->replace(['NOPE' => 'BAR']);
        $this->assertSame(['NOPE' => ['BAR']], $bag->all());
        $this->assertFalse($bag->has('Foo'));
        $this->assertTrue($bag->has('NOPE'));
        $this->assertArrayHasKey('nope', $bag->keys());
    }

    public function testGet(): void
    {
        $bag = new Headers(['foo' => 'bar', 'fuzz' => 'bizz']);

        $this->assertSame(['bar'], $bag->get('foo'));
        $this->assertSame(['bar'], $bag->get('FoO'));
        $this->assertSame(['bar'], $bag->all('foo'));

        // defaults
        $this->assertEmpty($bag->get('none'));
        $this->assertSame([], $bag->all('none'));

        $bag->set('foo', 'bor', false);
        $this->assertSame(['bar', 'bor'], $bag->get('foo'));
        $this->assertSame(['bar', 'bor'], $bag->all('foo'));
    }

    public function testGetLine(): void
    {
        $bag = new Headers(['foo' => 'bar', 'fuzz' => 'bizz']);

        $this->assertSame('bar', $bag->getLine('foo'));
        $this->assertSame('bar', $bag->getLine('FoO'));

        $bag->set('foo', 'foo');
        $this->assertSame('foo', $bag->getLine('foo'));
        $this->assertSame('foo', $bag->getLine('FoO'));

        $bag->set('foo', 'bar', false);
        $this->assertSame('foo, bar', $bag->getLine('foo'));
    }

    public function testSetAssociativeArray(): void
    {
        $bag = new Headers();
        $bag->set('foo', ['bad-assoc-index' => 'value']);

        $this->assertSame(['value'], $bag->get('foo'));
        $this->assertSame(['value'], $bag->all('foo'));
    }

    public function testContains(): void
    {
        $bag = new Headers(['foo' => 'bar', 'fuzz' => 'bizz']);
        $this->assertTrue($bag->contains('foo', 'bar'), '->contains first value');
        $this->assertTrue($bag->contains('fuzz', 'bizz'), '->contains second value');
        $this->assertFalse($bag->contains('nope', 'nope'), '->contains unknown value');
        $this->assertFalse($bag->contains('foo', 'nope'), '->contains unknown value');

        // Multiple values
        $bag->set('foo', 'bor', false);
        $this->assertTrue($bag->contains('foo', 'bar'), '->contains first value');
        $this->assertTrue($bag->contains('foo', 'bor'), '->contains second value');
        $this->assertFalse($bag->contains('foo', 'nope'), '->contains unknown value');
    }

    public function testCacheControlDirectiveAccessors(): void
    {
        $bag = new Headers();
        $bag->addCacheControlDirective('public');

        $this->assertTrue($bag->hasCacheControlDirective('public'));
        $this->assertTrue($bag->getCacheControlDirective('public'));
        $this->assertSame(['public'], $bag->get('Cache-Control'));

        $bag->addCacheControlDirective('max-age', '10');
        $this->assertTrue($bag->hasCacheControlDirective('max-age'));
        $this->assertSame('10', $bag->getCacheControlDirective('max-age'));
        $this->assertSame(['max-age=10, public'], $bag->get('Cache-Control'));

        $bag->removeCacheControlDirective('max-age');
        $this->assertFalse($bag->hasCacheControlDirective('max-age'));
    }

    public function testCacheControlDirectiveParsing(): void
    {
        $bag = new Headers(['Cache-Control' => 'public, max-age=10']);
        $this->assertTrue($bag->hasCacheControlDirective('public'));
        $this->assertTrue($bag->getCacheControlDirective('public'));

        $this->assertTrue($bag->hasCacheControlDirective('max-age'));
        $this->assertSame('10', $bag->getCacheControlDirective('max-age'));

        $bag->addCacheControlDirective('s-max-age', '100');
        $this->assertSame(['max-age=10, public, s-max-age=100'], $bag->get('Cache-Control'));
    }

    public function testCacheControlDirectiveParsingQuotedZero(): void
    {
        $bag = new Headers(['cache-control' => 'max-age="0"']);
        $this->assertTrue($bag->hasCacheControlDirective('max-age'));
        $this->assertSame('0', $bag->getCacheControlDirective('max-age'));
    }

    public function testCacheControlDirectiveOverrideWithReplace(): void
    {
        $bag = new Headers(['cache-control' => 'private, max-age=100']);
        $bag->replace(['cache-control' => 'public, max-age=10']);
        $this->assertTrue($bag->hasCacheControlDirective('public'));
        $this->assertTrue($bag->getCacheControlDirective('public'));

        $this->assertTrue($bag->hasCacheControlDirective('max-age'));
        $this->assertSame('10', $bag->getCacheControlDirective('max-age'));
    }

    public function testCacheControlClone(): void
    {
        $headers = ['foo' => 'bar'];
        $bag1 = new Headers($headers);
        $bag2 = new Headers($bag1->all());

        $this->assertSame($bag1->all(), $bag2->all());
    }

    public function testGetIterator(): void
    {
        $headers = ['foo' => 'bar', 'hello' => 'world', 'third' => 'charm'];
        $headerBag = new Headers($headers);

        $i = 0;
        foreach ($headerBag as $key => $val) {
            ++$i;
            $this->assertSame([$headers[$key]], $val);
        }

        $this->assertSame(\count($headers), $i);
    }

    public function testCount(): void
    {
        $headers = ['foo' => 'bar', 'HELLO' => 'WORLD'];
        $headerBag = new Headers($headers);

        $this->assertCount(\count($headers), $headerBag);
    }
}
