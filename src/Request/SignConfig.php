<?php

namespace Pff\EasyApi\Request;

use Illuminate\Support\Arr;
use Pff\EasyApi\API;

class SignConfig
{
    /**
     * @var string
     */
    protected $key;

    protected $position = API::SIGN_POSITION_HEAD;

    protected $appends = [];


    public function __construct($key, $position = null, $appends = [])
    {
        $this->key = $key;
        if (! is_null($position)) {
            $this->position = $position;
        }
        $this->appends = (array)$appends;
    }

    /**
     * @param array $config
     * @return static
     */
    public static function create(array $config = []): SignConfig
    {
        $key = Arr::get($config, 'key');
        $position = Arr::get($config, 'position');
        $appends = Arr::get($config, 'appends', []);
        return new static($key, $position, $appends);
    }

    /**
     * @return string
     */
    public function getKey(): string
    {
        return $this->key;
    }

    /**
     * @param string $key
     * @return SignConfig
     */
    public function setKey(string $key): SignConfig
    {
        $this->key = $key;
        return $this;
    }

    /**
     * @return string
     */
    public function getPosition(): string
    {
        return $this->position;
    }

    /**
     * @param string $position
     */
    public function setPosition(string $position)
    {
        $this->position = $position;
    }

    /**
     * @return array
     */
    public function getAppends(): array
    {
        return $this->appends;
    }

    /**
     * @param array $appends
     */
    public function setAppends(array $appends)
    {
        $this->appends = $appends;
    }
}
