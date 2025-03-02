<?php

declare(strict_types=1);

namespace Pff\EasyApi\Exception;

use Pff\EasyApi\Contracts\ApiExceptionInterface;
use Pff\EasyApi\Exception\Contracts\ContextTrait;

class ApiException extends \Exception implements ApiExceptionInterface
{
    use ContextTrait;

    /**
     * @param array<string, mixed> $context
     */
    public function __construct(string $message = '', array $context = [], int $code = 0, ?\Throwable $previous = null)
    {
        parent::__construct($message, $code, $previous);
        $this->setContext($context);
    }
}
