<?php

namespace Pff\EasyApiTest\Unit\Concerns\stubs;

use ArrayAccess;
use Countable;
use IteratorAggregate;
use Pff\EasyApi\Concerns\DataTrait;

/**
 * @property string $bar
 * @property string $foo
 * @property string $foobar
 */
class DataTraitStub implements ArrayAccess, IteratorAggregate, Countable
{
    use DataTrait;

    public function __construct(array $data)
    {
        $this->collection($data);
    }
}
