<?php

declare(strict_types=1);

namespace Pff\EasyApi;

use Illuminate\Support\Arr;
use Pff\EasyApi\Contracts\ConfigInterface;

class Config implements ConfigInterface
{
    /**
     * @var array<string, mixed>
     */
    protected $item;

    /**
     * 创建配置对象。
     *
     * @param array<string, mixed> $config
     */
    final public function __construct(array $config)
    {
        $this->item = $config;
    }

    /**
     * {@inheritDoc}
     */
    public static function create(array $config): ConfigInterface
    {
        return new static($config);
    }

    /**
     * {@inheritDoc}
     */
    public function client(string $name, $defaultValue = null)
    {
        return $this->get('config.' . $name, $defaultValue);
    }

    /**
     * {@inheritDoc}
     */
    public function request(string $name, $defaultValue = null)
    {
        return $this->get('request.' . $name, $defaultValue);
    }

    /**
     * {@inheritDoc}
     */
    public function requestMethod(string $default = null): string
    {
        /** @var string */
        return $this->request('method', $default);
    }

    /**
     * {@inheritDoc}
     */
    public function requestUri(): string
    {
        /** @var string */
        return $this->request('uri');
    }

    /**
     * {@inheritDoc}
     */
    public function requestSandboxUri(): string
    {
        /** @var string */
        return $this->request('sandbox_uri');
    }

    /**
     * {@inheritDoc}
     */
    public function requestCache(): string
    {
        /** @var class-string */
        return $this->request('cache');
    }

    /**
     * {@inheritDoc}
     */
    public function requestFormatter(): string
    {
        /** @var class-string */
        return $this->request('formatter');
    }

    /**
     * {@inheritDoc}
     */
    public function requestFormat(string $default = API::RESPONSE_FORMAT_JSON): string
    {
        /** @var string */
        return $this->request('format', $default);
    }

    /**
     * {@inheritDoc}
     */
    public function requestSign(): array
    {
        /** @var array<string, string|string[]> */
        return $this->request('sign');
    }

    /**
     * {@inheritDoc}
     */
    public function requestSignature(): string
    {
        /** @var class-string */
        return $this->request('signature');
    }

    /**
     * {@inheritDoc}
     */
    public function all(): array
    {
        return $this->item;
    }

    /**
     * {@inheritDoc}
     */
    public function set(string $name, $newValue): ConfigInterface
    {
        Arr::set($this->item, $name, $newValue);

        return $this;
    }

    /**
     * {@inheritDoc}
     */
    public function get(string $name, $defaultValue = null)
    {
        return Arr::get($this->item, $name, $defaultValue);
    }

    /**
     * {@inheritDoc}
     */
    public function has(string $name): bool
    {
        return Arr::has($this->item, $name);
    }

    /**
     * {@inheritDoc}
     */
    public function remove(string $name): ConfigInterface
    {
        Arr::forget($this->item, $name);

        return $this;
    }
}
