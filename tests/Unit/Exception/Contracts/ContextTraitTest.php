<?php

declare(strict_types=1);

namespace Pff\EasyApiTest\Unit\Exception\Contracts;

use Pff\EasyApiTest\TestCase;
use Pff\EasyApiTest\Unit\Exception\Contracts\stubs\ContextException;

/**
 * @internal
 *
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
        self::assertSame($context, $e->context());

        $e->mergeContext(['bar' => 'bar']);
        $context['bar'] = 'bar';
        self::assertSame($context, $e->context());

        $e->setContext(['foobar' => 'foobar']);
        self::assertSame(['foobar' => 'foobar'], $e->context());
        self::assertArrayNotHasKey('bar', $e->context());
    }

    public static function testContextWithException(): void
    {
        $context = [
            'uuid' => '1234567890',
            'foo' => 'foo',
        ];
        $e = new ContextException($context, new \Exception('exception message.'));
        self::assertArrayHasKey('uuid', $e->context());
        self::assertArrayHasKey('foo', $e->context());
        self::assertArrayHasKey('exception', $e->context());

        $e->mergeContext(['bar' => 'bar']);
        self::assertArrayHasKey('bar', $e->context());

        $e->setContext(['foobar' => 'foobar']);
        self::assertArrayHasKey('foobar', $e->context());
        self::assertArrayNotHasKey('uuid', $e->context());
    }
}
