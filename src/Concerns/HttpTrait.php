<?php

declare(strict_types=1);

namespace Pff\EasyApi\Concerns;

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
     */
    public function addQuery($query, $value = null): self
    {
        if (null === $value) {
            $this->query->add($query);

            return $this;
        }

        $this->query->set($query, $value);

        return $this;
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
     * @param array<string, mixed>|string $query
     */
    public function setQuery($query): self
    {
        $this->query->replace($query);

        return $this;
    }

    /**
     * @param array<string, mixed>|string $key
     * @param mixed $value
     */
    public function addData($key, $value = null): self
    {
        if (null === $value) {
            $this->data->add($key);

            return $this;
        }

        $this->data->set($key, $value);

        return $this;
    }

    public function getData(): Parameters
    {
        return $this->data;
    }

    /**
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
     * @param array<string, mixed>|string $key
     * @param mixed $value
     */
    public function addHeaders($key, $value = null): self
    {
        if (null === $value) {
            $this->headers->add($key);

            return $this;
        }

        $this->headers->set($key, $value);

        return $this;
    }

    public function getHeaders(): Headers
    {
        return $this->headers;
    }

    /**
     * @param array<string, mixed> $headers
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
     * @removed 1.0
     *
     * @return $this|Parameters
     */
    public function query(array $query = null, bool $replace = false)
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
     * @removed 1.0
     *
     * @return $this|Parameters
     */
    public function data(array $post = null, bool $replace = false)
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
     * @param false $replace
     *
     * @return $this|Headers
     *@deprecated 0.1.3 addHeaders(), setHeaders(), getHeaders()
     * @removed 1.0
     *
     */
    public function headers(array $headers = null, bool $replace = false)
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
     * @param array|string $proxy
     *
     * @return $this
     */
    public function proxy($proxy): self
    {
        $this->options['proxy'] = $proxy;

        return $this;
    }

    /**
     * @param mixed $verify
     *
     * @return $this
     */
    public function verify($verify): self
    {
        $this->options['verify'] = $verify;

        return $this;
    }

    /**
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
