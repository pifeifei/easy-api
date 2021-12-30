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
     * @param string $method
     * @param UriInterface $uri
     * @param array $options
     * @param ConfigInterface $config
     */
    public function __construct(string $method, UriInterface $uri, array $options, ConfigInterface $config)
    {
        $this->method = strtoupper($method);
        $this->uri = $uri;
        $this->options = $options;
        $this->config = $config;
    }

    public function getConfig()
    {
        return $this->config;
    }

    /**
     * @return string
     */
    public function getMethod(): string
    {
        return $this->method;
    }

    /**
     * @return UriInterface
     */
    public function getUri(): UriInterface
    {
        return $this->uri;
    }

    /**
     * @return array
     */
    public function getOptions(): array
    {
        return $this->options;
    }

    /**
     * @return string
     */
    public function format(): string
    {
        return $this->config->request('format');
    }
}
