<?php

declare(strict_types=1);

namespace Pff\EasyApiTest\Unit\Exception\Contracts;

use Pff\EasyApiTest\TestCase;
use Pff\EasyApiTest\Unit\Exception\Contracts\stubs\ContextException;

/**
 * @internal
 * @coversNothing
 */
final class ContextTraitTest extends TestCase
{
    public static function testContext(): void
    {
        $context = [
            'uuid' => '1234567890',
            'foo' => 'foo',
        ];
        $e = new ContextException($context);
        static::assertSame($context, $e->context());

        $e->mergeContext(['bar' => 'bar']);
        $context['bar'] = 'bar';
        static::assertSame($context, $e->context());

        $e->setContext(['foobar' => 'foobar']);
        static::assertSame(['foobar' => 'foobar'], $e->context());
        static::assertArrayNotHasKey('bar', $e->context());
    }

    public static function testContextWithException(): void
    {
        $context = [
            'uuid' => '1234567890',
            'foo' => 'foo',
        ];
        $e = new ContextException($context, new \Exception('exception message.'));
        static::assertArrayHasKey('uuid', $e->context());
        static::assertArrayHasKey('foo', $e->context());
        static::assertArrayHasKey('exception', $e->context());

        $e->mergeContext(['bar' => 'bar']);
        static::assertArrayHasKey('bar', $e->context());

        $e->setContext(['foobar' => 'foobar']);
        static::assertArrayHasKey('foobar', $e->context());
        static::assertArrayNotHasKey('uuid', $e->context());
    }
}
