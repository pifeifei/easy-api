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

    public static function create(array $config): ConfigInterface
    {
        return new static($config);
    }

    public function client(string $name, $defaultValue = null)
    {
        return $this->get('config.' . $name, $defaultValue);
    }

    public function request(string $name, $defaultValue = null)
    {
        return $this->get('request.' . $name, $defaultValue);
    }

    public function requestMethod(string $default = null): string
    {
        /** @var string */
        return $this->request('method', $default);
    }

    public function requestUri(): string
    {
        /** @var string */
        return $this->request('uri');
    }

    public function requestSandboxUri(): string
    {
        /** @var string */
        return $this->request('sandbox_uri');
    }

    public function requestCache(): string
    {
        /** @var class-string */
        return $this->request('cache');
    }

    public function requestFormatter(): string
    {
        /** @var class-string */
        return $this->request('formatter');
    }

    public function requestFormat(string $default = API::RESPONSE_FORMAT_JSON): string
    {
        /** @var string */
        return $this->request('format', $default);
    }

    public function requestSign(): array
    {
        /** @var array<string, string|string[]> */
        return $this->request('sign');
    }

    public function requestSignature(): string
    {
        /** @var class-string */
        return $this->request('signature');
    }

    public function all(): array
    {
        return $this->item;
    }

    public function set(string $name, $newValue): ConfigInterface
    {
        Arr::set($this->item, $name, $newValue);

        return $this;
    }

    public function get(string $name, $defaultValue = null)
    {
        return Arr::get($this->item, $name, $defaultValue);
    }

    public function has(string $name): bool
    {
        return Arr::has($this->item, $name);
    }

    public function remove(string $name): ConfigInterface
    {
        Arr::forget($this->item, $name);

        return $this;
    }
}
