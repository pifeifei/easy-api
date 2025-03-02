<?php

declare(strict_types=1);

namespace Pff\EasyApi\Contracts;

use Pff\EasyApi\API;

interface ConfigInterface
{
    /**
     * 创建配置对象。
     *
     * @param array<string, mixed> $config
     */
    public static function create(array $config): self;

    /**
     * 获取客户端的配置信息。
     *
     * @param null|mixed $defaultValue
     *
     * @return mixed
     */
    public function client(string $name, $defaultValue = null);

    /**
     * 获取请求的配置。
     *
     * @param null|mixed $defaultValue
     *
     * @return mixed
     */
    public function request(string $name, $defaultValue = null);

    /**
     * 获取请求方式。
     */
    public function requestMethod(?string $default = null): string;

    /**
     * 获取请求链接。
     */
    public function requestUri(): string;

    /**
     * 获取沙箱请求链接。
     */
    public function requestSandboxUri(): string;

    /**
     * 获取缓存类名。
     *
     * @return class-string
     */
    public function requestCache(): string;

    /**
     * 获取格式化类名。
     *
     * @return class-string
     */
    public function requestFormatter(): string;

    /**
     * 获取响应数据类型，允许自定义。
     */
    public function requestFormat(string $default = API::RESPONSE_FORMAT_JSON): string;

    /**
     * 获取签名的配置。
     *
     * @return array<string, string|string[]>
     */
    public function requestSign(): array;

    /**
     * 获取签名类名。
     *
     * @return class-string
     */
    public function requestSignature(): string;

    /**
     * 获取所有配置。
     *
     * @return array<string, mixed>
     */
    public function all(): array;

    /**
     * 设置属性。
     *
     * @param bool|float|int|string $newValue
     *
     * @deprecated 0.1.1 创建实例后配置将为只读。
     *
     * @removed 1.0
     */
    public function set(string $name, $newValue): self;

    /**
     * 获取属性。
     *
     * @param mixed $defaultValue
     *
     * @return mixed
     */
    public function get(string $name, $defaultValue = null);

    /**
     * 判断是否存在键。
     */
    public function has(string $name): bool;

    /**
     * 移除某个属性。
     */
    public function remove(string $name): self;
}
