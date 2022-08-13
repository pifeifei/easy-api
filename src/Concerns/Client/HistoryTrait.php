<?php

declare(strict_types=1);

namespace Pff\EasyApi\Concerns\Client;

use Psr\Http\Message\RequestInterface;

trait HistoryTrait
{
    /**
     * @var array{request: RequestInterface, response: null|object, error: null|object, option: array<int|string>}
     */
    protected $histories = [];

    /**
     * @var bool
     */
    protected $isRememberHistory = false;

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

    public function &referenceHistory(): array
    {
        return $this->histories;
    }

    public function countHistory(): int
    {
        return \count($this->histories);
    }
}
