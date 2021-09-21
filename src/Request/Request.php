<?php

namespace Pff\EasyApi\Request;

use Pff\EasyApi\Contracts\ConfigInterface;
use Psr\Http\Message\UriInterface;

class Request
{
    /**
     * @var string
     */
    protected $config;

    /**
     * @var string
     */
    protected $method;

    /**
     * @var UriInterface
     */
    protected $uri;

    /**
     * @var array
     */
    protected $options;

    /**
     * @param  string  $method
     * @param  UriInterface  $uri
     * @param  array  $options
     * @param  ConfigInterface  $config
     */
    public function __construct($method, $uri, $options, $config)
    {
        $this->method = $method;
        $this->uri = $uri;
        $this->options = $options;
        $this->config = $config;
    }

    /**
     * @return string
     */
    public function getMethod()
    {
        return $this->method;
    }

    /**
     * @return UriInterface
     */
    public function getUri()
    {
        return $this->uri;
    }

    /**
     * @return array
     */
    public function getOptions()
    {
        return $this->options;
    }

    /**
     * @return string
     */
    public function format()
    {
        return $this->config->request('format');
    }
}
