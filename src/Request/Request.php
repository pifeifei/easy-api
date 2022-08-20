<?php

declare(strict_types=1);

namespace Pff\EasyApi\Request;

use Pff\EasyApi\Contracts\ConfigInterface;
use Psr\Http\Message\UriInterface;

class Request
{
    protected ConfigInterface $config;

    protected string $method;

    protected UriInterface $uri;

    /**
     * @var array<string, mixed>
     */
    protected $options;

    /**
     * @param array<string, mixed> $options
     */
    public function __construct(string $method, UriInterface $uri, array $options, ConfigInterface $config)
    {
        $this->method = strtoupper($method);
        $this->uri = $uri;
        $this->options = $options;
        $this->config = $config;
    }

    public function getConfig(): ConfigInterface
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

    /**
     * @return array<string, mixed>
     */
    public function getOptions(): array
    {
        return $this->options;
    }

    public function format(): ?string
    {
        return $this->config->requestFormat();
    }
}
