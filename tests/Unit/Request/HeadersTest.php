<?php

declare(strict_types=1);

namespace Pff\EasyApiTest\Unit\Request;

use Pff\EasyApi\Request\Headers;
use Pff\EasyApiTest\TestCase;

/**
 * @internal
 * @coversNothing
 */
final class HeadersTest extends TestCase
{
    public function testConstructor(): void
    {
        $bag = new Headers(['foo' => 'bar']);
        static::assertTrue($bag->has('foo'));
    }

    public function testKeys(): void
    {
        $bag = new Headers(['foo' => 'bar']);
        $keys = $bag->keys();
        static::assertSame('foo', $keys[0]);
    }

    public function testGetDate(): void
    {
        $bag = new Headers(['foo' => 'Tue, 4 Sep 2012 20:00:00 +0200']);
        $headerDate = $bag->getDate('foo');
        static::assertInstanceOf(\DateTime::class, $headerDate);
    }

    public function testGetDateNull(): void
    {
        $bag = new Headers(['foo' => null]);
        $headerDate = $bag->getDate('foo');
        static::assertNull($headerDate);
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
        static::assertTrue($bag->hasCacheControlDirective('public'));
        static::assertSame('#a', $bag->getCacheControlDirective('public'));
    }

    public function testAll(): void
    {
        $bag = new Headers(['foo' => 'bar']);
        static::assertSame(['foo' => ['bar']], $bag->all(), '->all() gets all the input');

        $bag = new Headers(['FOO' => 'BAR']);
        static::assertSame(['foo' => ['BAR']], $bag->all(), '->all() gets all the input key are lower case');
    }

    public function testReplace(): void
    {
        $bag = new Headers(['foo' => 'bar']);

        $bag->replace(['NOPE' => 'BAR']);
        static::assertSame(['nope' => ['BAR']], $bag->all(), '->replace() replaces the input with the argument');
        static::assertFalse($bag->has('foo'), '->replace() overrides previously set the input');
    }

    public function testGet(): void
    {
        $bag = new Headers(['foo' => 'bar', 'fuzz' => 'bizz']);
        static::assertSame('bar', $bag->get('foo'), '->get return current value');
        static::assertSame('bar', $bag->get('FoO'), '->get key in case insensitive');
        static::assertSame(['bar'], $bag->all('foo'), '->get return the value as array');

        // defaults
        static::assertNull($bag->get('none'), '->get unknown values returns null');
        static::assertSame('default', $bag->get('none', 'default'), '->get unknown values returns default');
        static::assertSame([], $bag->all('none'), '->get unknown values returns an empty array');

        $bag->set('foo', 'bor', false);
        static::assertSame('bar', $bag->get('foo'), '->get return first value');
        static::assertSame(['bar', 'bor'], $bag->all('foo'), '->get return all values as array');

        $bag->set('baz', null);
        static::assertNull($bag->get('baz', 'nope'), '->get return null although different default value is given');
    }

    public function testSetAssociativeArray(): void
    {
        $bag = new Headers();
        $bag->set('foo', ['bad-assoc-index' => 'value']);
        static::assertSame('value', $bag->get('foo'));
        static::assertSame(['value'], $bag->all('foo'), 'assoc indices of multi-valued headers are ignored');
    }

    public function testContains(): void
    {
        $bag = new Headers(['foo' => 'bar', 'fuzz' => 'bizz']);
        static::assertTrue($bag->contains('foo', 'bar'), '->contains first value');
        static::assertTrue($bag->contains('fuzz', 'bizz'), '->contains second value');
        static::assertFalse($bag->contains('nope', 'nope'), '->contains unknown value');
        static::assertFalse($bag->contains('foo', 'nope'), '->contains unknown value');

        // Multiple values
        $bag->set('foo', 'bor', false);
        static::assertTrue($bag->contains('foo', 'bar'), '->contains first value');
        static::assertTrue($bag->contains('foo', 'bor'), '->contains second value');
        static::assertFalse($bag->contains('foo', 'nope'), '->contains unknown value');
    }

    public function testCacheControlDirectiveAccessors(): void
    {
        $bag = new Headers();
        $bag->addCacheControlDirective('public');

        static::assertTrue($bag->hasCacheControlDirective('public'));
        static::assertTrue($bag->getCacheControlDirective('public'));
        static::assertSame('public', $bag->get('cache-control'));

        $bag->addCacheControlDirective('max-age', '10');
        static::assertTrue($bag->hasCacheControlDirective('max-age'));
        static::assertSame('10', $bag->getCacheControlDirective('max-age'));
        static::assertSame('max-age=10, public', $bag->get('cache-control'));

        $bag->removeCacheControlDirective('max-age');
        static::assertFalse($bag->hasCacheControlDirective('max-age'));
    }

    public function testCacheControlDirectiveParsing(): void
    {
        $bag = new Headers(['cache-control' => 'public, max-age=10']);
        static::assertTrue($bag->hasCacheControlDirective('public'));
        static::assertTrue($bag->getCacheControlDirective('public'));

        static::assertTrue($bag->hasCacheControlDirective('max-age'));
        static::assertSame('10', $bag->getCacheControlDirective('max-age'));

        $bag->addCacheControlDirective('s-maxage', '100');
        static::assertSame('max-age=10, public, s-maxage=100', $bag->get('cache-control'));
    }

    public function testCacheControlDirectiveParsingQuotedZero(): void
    {
        $bag = new Headers(['cache-control' => 'max-age="0"']);
        static::assertTrue($bag->hasCacheControlDirective('max-age'));
        static::assertSame('0', $bag->getCacheControlDirective('max-age'));
    }

    public function testCacheControlDirectiveOverrideWithReplace(): void
    {
        $bag = new Headers(['cache-control' => 'private, max-age=100']);
        $bag->replace(['cache-control' => 'public, max-age=10']);
        static::assertTrue($bag->hasCacheControlDirective('public'));
        static::assertTrue($bag->getCacheControlDirective('public'));

        static::assertTrue($bag->hasCacheControlDirective('max-age'));
        static::assertSame('10', $bag->getCacheControlDirective('max-age'));
    }

    public function testCacheControlClone(): void
    {
        $headers = ['foo' => 'bar'];
        $bag1 = new Headers($headers);
        $bag2 = new Headers($bag1->all());

        static::assertSame($bag1->all(), $bag2->all());
    }

    public function testGetIterator(): void
    {
        $headers = ['foo' => 'bar', 'hello' => 'world', 'third' => 'charm'];
        $headerBag = new Headers($headers);

        $i = 0;
        foreach ($headerBag as $key => $val) {
            ++$i;
            static::assertSame([$headers[$key]], $val);
        }

        static::assertSame(\count($headers), $i);
    }

    public function testCount(): void
    {
        $headers = ['foo' => 'bar', 'HELLO' => 'WORLD'];
        $headerBag = new Headers($headers);

        static::assertCount(\count($headers), $headerBag);
    }
}
