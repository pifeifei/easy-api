<?php

declare(strict_types=1);

namespace Pff\EasyApi\Concerns;

use Pff\EasyApi\Exception\ClientException;
use Pff\EasyApi\Request\Headers;
use Pff\EasyApi\Request\Parameters;

trait HttpTrait
{
    /**
     * 参考 guzzle http 的 options 。
     *
     * @var array<string, mixed>
     */
    protected array $options = [];

    /**
     * 请求参数（$_GET）。
     */
    protected Parameters $query;

    /**
     * 请求体（$_POST）。
     *
     * @description POST 请求放到到 body 请求体，GET 请求会合并到 uri 的 query string 中。
     */
    protected Parameters $data;

    /**
     * 请求头。
     */
    protected Headers $headers;

    /**
     * 添加单个参数。
     *
     * @param array<string, mixed>|string $query
     * @param mixed $value
     *
     * @throws ClientException
     */
    public function addQuery($query, $value = null): self
    {
        if (\is_array($query)) {
            $this->query->add($query);

            return $this;
        }

        if (null !== $value) {
            $this->query->set($query, $value);

            return $this;
        }

        throw new ClientException("GET 参数设置失败：{$query}");
    }

    /**
     * 获取参数。
     */
    public function getQuery(): Parameters
    {
        return $this->query;
    }

    /**
     * 设置参数。
     *
     * @param array<string, mixed> $query
     */
    public function setQuery(array $query): self
    {
        $this->query->replace($query);

        return $this;
    }

    /**
     * 添加单个请求数据。
     *
     * @param array<string, mixed>|string $key
     * @param mixed $value
     *
     * @throws ClientException
     */
    public function addData($key, $value = null): self
    {
        if (\is_array($key)) {
            $this->data->add($key);

            return $this;
        }

        if (null !== $value) {
            $this->data->set($key, $value);

            return $this;
        }

        throw new ClientException("POST 参数设置失败：{$key}");
    }

    public function getData(): Parameters
    {
        return $this->data;
    }

    /**
     * 设置请求数据。
     *
     * @param array<string, mixed> $data
     *
     * @return $this
     */
    public function setData(array $data): self
    {
        $this->data->replace($data);

        return $this;
    }

    /**
     * 设置请求头。
     *
     * @param string|string[] $values
     *
     * @return $this
     */
    public function setHeader(string $key, $values, bool $replace = true): self
    {
        $this->headers->set($key, $values, $replace);

        return $this;
    }

    /**
     * 批量添加请求头。
     *
     * @param array<string, string|string[]> $headers
     */
    public function addHeaders(array $headers): self
    {
        foreach ($headers as $headerName => $header) {
            $this->setHeader($headerName, $header);
        }

        return $this;
    }

    public function getHeaders(): Headers
    {
        return $this->headers;
    }

    /**
     * @param array<string, string|string[]> $headers
     *
     * @return $this
     */
    public function setHeaders(array $headers): self
    {
        $this->headers->replace($headers);

        return $this;
    }

    /**
     * @deprecated 0.1.3 addQuery(), setQuery(), getQuery()
     *
     * @removed 1.0
     *
     * @param array<string, mixed> $query
     *
     * @return $this|Parameters
     */
    public function query(?array $query = null, bool $replace = false)
    {
        if (null === $query) {
            return $this->query;
        }

        if ($replace) {
            $this->query->replace($query);

            return $this;
        }

        $this->query->add($query);

        return $this;
    }

    /**
     * @deprecated 0.1.3 addData(), setData(), getData()
     *
     * @removed 1.0
     *
     * @param array<string, mixed> $post
     *
     * @return $this|Parameters
     */
    public function data(?array $post = null, bool $replace = false)
    {
        if (null === $post) {
            return $this->data;
        }

        if ($replace) {
            $this->data->replace($post);

            return $this;
        }

        $this->data->add($post);

        return $this;
    }

    /**
     * @deprecated 0.1.3 addHeaders(), setHeaders(), getHeaders()
     *
     * @removed 1.0
     *
     * @param null|array<string, string|string[]> $headers
     *
     * @return $this|Headers
     */
    public function headers(?array $headers = null, bool $replace = false)
    {
        if (null === $headers) {
            return $this->headers;
        }

        if ($replace) {
            $this->headers->replace($headers);

            return $this;
        }

        $this->headers->add($headers);

        return $this;
    }

    /**
     * @return $this
     */
    public function timeout(float $seconds): self
    {
        $this->options['timeout'] = $seconds;

        return $this;
    }

    /**
     * @return $this
     */
    public function timeoutMilliseconds(int $milliseconds): self
    {
        $seconds = $milliseconds / 1000;

        return $this->timeout($seconds);
    }

    /**
     * @return $this
     */
    public function connectTimeout(float $seconds): self
    {
        $this->options['connect_timeout'] = $seconds;

        return $this;
    }

    /**
     * @return $this
     */
    public function connectTimeoutMilliseconds(int $milliseconds): self
    {
        $seconds = $milliseconds / 1000;

        return $this->connectTimeout($seconds);
    }

    /**
     * @return $this
     */
    public function debug(bool $debug): self
    {
        $this->options['debug'] = $debug;

        return $this;
    }

    /**
     * @codeCoverageIgnore
     *
     * @param string|string[] $cert
     *
     * @return $this
     */
    public function cert($cert): self
    {
        $this->options['cert'] = $cert;

        return $this;
    }

    /**
     * @param array<string, string|string[]>|string $proxy
     *
     * @return $this
     */
    public function proxy($proxy): self
    {
        $this->options['proxy'] = $proxy;

        return $this;
    }

    /**
     * @param bool|string $verify 是否启用证书访问接口。
     *                            设置成字符串启用验证，并使用该字符串作为自定义证书CA包的路径。
     *
     * @return $this
     */
    public function verify($verify): self
    {
        $this->options['verify'] = $verify;

        return $this;
    }

    /**
     * @param array<string, mixed> $options
     *
     * @return $this
     */
    public function options(array $options): self
    {
        if ([] !== $options) {
            $this->options = array_merge($this->options, $options);
        }

        return $this;
    }

    /**
     * @return array<string, mixed>
     */
    public function getOptions(): array
    {
        return $this->options;
    }
}
