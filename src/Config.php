<?php

namespace Pff\EasyApi;

use Illuminate\Support\Arr;
use Pff\EasyApi\Contracts\ConfigInterface;

class Config implements ConfigInterface
{
    protected $item = [];

    public function __construct(array $config)
    {
        $this->item = $config;
    }

    /**
     * @inheritDoc
     */
    public static function create(array $config)
    {
        return new static($config);
    }

    public function client($name, $defaultValue = null)
    {
        return $this->get('config.' . $name, $defaultValue);
    }

    public function request($name, $defaultValue = null)
    {
        return $this->get('request.' . $name, $defaultValue);
    }

    /**
     * @inheritDoc
     */
    public function all()
    {
        return $this->item;
    }

    /**
     * @inheritDoc
     */
    public function set($name, $newValue = null)
    {
        Arr::set($this->item, $name, $newValue);
        return $this;
    }

    /**
     * @inheritDoc
     */
    public function get($name, $defaultValue = null)
    {
        return Arr::get($this->item, $name, $defaultValue);
    }

    /**
     * @inheritDoc
     */
    public function has($name)
    {
        return Arr::has($this->item, $name);
    }

    /**
     * @inheritDoc
     */
    public function remove($name)
    {
        Arr::forget($this->item, $name);
        return $this;
    }
}
