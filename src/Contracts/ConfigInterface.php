<?php

namespace Pff\EasyApi\Contracts;

interface ConfigInterface
{
    /**
     * create a Config
     *
     * @param array $config
     * @return ConfigInterface
     */
    public static function create(array $config): ConfigInterface;

    /**
     * @param string $name
     * @param scalar|array $defaultValue
     * @return scalar|array
     */
    public function client(string $name, $defaultValue = null);

    /**
     * @param string $name
     * @param scalar|array $defaultValue
     * @return scalar|array
     */
    public function request(string $name, $defaultValue = null);

    /**
     * @return array
     */
    public function all(): array;

    /**
     * 设置属性
     *
     * @param string|array $name
     * @param scalar|array $newValue
     * @return ConfigInterface
     */
    public function set($name, $newValue = null): ConfigInterface;

    /**
     * 获取属性
     *
     * @param string $name
     * @param scalar|array|null $defaultValue
     * @return scalar|array
     */
    public function get(string $name, $defaultValue = null);

    /**
     * 判断是否存在键
     * @param $name
     * @return bool
     */
    public function has($name): bool;

    /**
     * 移除某个属性
     *
     * @param string $name
     * @return ConfigInterface
     */
    public function remove(string $name): ConfigInterface;

}
