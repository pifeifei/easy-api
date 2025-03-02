<?php

declare(strict_types=1);

namespace Pff\EasyApiTest\Unit\Exception\Contracts\stubs;

use Pff\EasyApi\Contracts\ApiExceptionInterface;
use Pff\EasyApi\Exception\Contracts\ContextTrait;

class ContextException extends \Exception implements ApiExceptionInterface
{
    use ContextTrait;

    /**
     * @param array<string, mixed> $context
     */
    public function __construct(array $context = [], ?\Throwable $previous = null)
    {
        parent::__construct('text exception.', 0, $previous);
        $this->setContext($context);
    }
}
