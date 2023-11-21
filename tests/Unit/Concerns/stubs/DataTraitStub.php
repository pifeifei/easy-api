<?php

declare(strict_types=1);

namespace Pff\EasyApiTest\Unit\Concerns\stubs;

use Pff\EasyApi\Concerns\DataTrait;

/**
 * @property string $bar
 * @property string $foo
 * @property string $foobar
 */
class DataTraitStub implements \ArrayAccess, \IteratorAggregate, \Countable
{
    use DataTrait;

    public function __construct(array $data)
    {
        $this->collection($data);
    }
}
