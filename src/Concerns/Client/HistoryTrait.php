<?php

namespace Pff\EasyApi\Concerns\Client;


trait HistoryTrait
{
    /**
     * @var array
     */
    protected $histories = [];

    /**
     * @var bool
     */
    protected $isRememberHistory = false;

    /**
     * @return array
     */
    public function getHistory(): array
    {
        return $this->histories;
    }

    public function forgetHistory()
    {
        $this->histories = [];
    }

    /**
     * @param bool $remember
     */
    public function rememberHistory(bool $remember = true)
    {
        $this->isRememberHistory = $remember;
    }

    /**
     * @return bool
     */
    public function isRememberHistory(): bool
    {
        return $this->isRememberHistory;
    }

    /**
     * @return array
     */
    public function &referenceHistory(): array
    {
        return $this->histories;
    }

    /**
     * @return int
     */
    public function countHistory(): int
    {
        return count($this->histories);
    }
}
