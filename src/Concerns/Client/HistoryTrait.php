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
    public function getHistory()
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
    public function rememberHistory($remember = true)
    {
        $this->isRememberHistory = $remember;
    }

    /**
     * @return bool
     */
    public function isRememberHistory()
    {
        return $this->isRememberHistory;
    }

    /**
     * @return array
     */
    public function &referenceHistory()
    {
        return $this->histories;
    }

    /**
     * @return int
     */
    public function countHistory()
    {
        return count($this->histories);
    }
}
