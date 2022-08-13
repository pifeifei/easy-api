<?php

declare(strict_types=1);

namespace Pff\EasyApi\Exception;

use Pff\EasyApi\Request\Request;
use Pff\EasyApi\Result;
use Throwable;

class ServerException extends ApiException
{
    /**
     * @var Result
     */
    protected $result;

    /**
     * {@inheritDoc}
     *
     * @param array<string, mixed> $context
     */
    public function __construct(Result $result, string $message = '', array $context = [], int $code = 0, Throwable $previous = null)
    {
        parent::__construct($message, $context, $code, $previous);
        $this->result = $result;
    }

    public function getResult(): Result
    {
        return $this->result;
    }

    public function getRequest(): Request
    {
        return $this->getResult()->getRequest();
    }

    public function getResponse(): Result
    {
        return $this->getResult();
    }
}
