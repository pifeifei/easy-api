<?php

declare(strict_types=1);

namespace Pff\EasyApi\Exception;

use Exception;
use Pff\EasyApi\Contracts\ApiExceptionInterface;
use Pff\EasyApi\Exception\Contracts\ContextTrait;
use Throwable;

class ApiException extends Exception implements ApiExceptionInterface
{
    use ContextTrait;

    /**
     * {@inheritDoc}
     *
     * @param array<string, mixed> $context
     */
    public function __construct(string $message = '', array $context = [], int $code = 0, Throwable $previous = null)
    {
        parent::__construct($message, $code, $previous);
        $this->setContext($context);
    }
}
