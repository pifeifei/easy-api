<?php

declare(strict_types=1);

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

    public function getMethod(): string
    {
        return $this->method;
    }

    public function getUri(): UriInterface
    {
        return $this->uri;
    }

    public function getOptions(): array
    {
        return $this->options;
    }

    public function format(): string
    {
        return $this->config->request('format');
    }
}
