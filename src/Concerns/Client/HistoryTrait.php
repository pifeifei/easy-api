<?php

declare(strict_types=1);

namespace Pff\EasyApi\Concerns\Client;

use Pff\EasyApi\Result;
use Psr\Http\Message\RequestInterface;

trait HistoryTrait
{
    /**
     * @var array<array{request: RequestInterface, response: null|Result, error: null|object, option: array<int|string>}>
     */
    protected array $histories = [];

    protected bool $isRememberHistory = false;

    /**
     * @return array<array{request: RequestInterface, response: null|Result, error: null|object, option: array<int|string>}>
     */
    public function getHistory(): array
    {
        return $this->histories;
    }

    public function forgetHistory(): void
    {
        $this->histories = [];
    }

    public function rememberHistory(bool $remember = true): void
    {
        $this->isRememberHistory = $remember;
    }

    public function isRememberHistory(): bool
    {
        return $this->isRememberHistory;
    }

    /**
     * @return array<array{request: RequestInterface, response: null|Result, error: null|object, option: array<int|string>}>
     */
    public function &referenceHistory(): array
    {
        return $this->histories;
    }

    public function countHistory(): int
    {
        return \count($this->histories);
    }
}
