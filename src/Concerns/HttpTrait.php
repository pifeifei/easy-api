<?php

declare(strict_types=1);

namespace Pff\EasyApi\Concerns;

use Pff\EasyApi\Request\Headers;
use Pff\EasyApi\Request\Parameters;

trait HttpTrait
{
    protected $options = [];

    /**
     * @var Parameters
     */
    protected $query;

    /**
     * @var Parameters
     */
    protected $data;

    /**
     * @var Headers
     */
    protected $headers;

    /**
     * @param null|array $query
     * @param bool $replace
     *
     * @return $this|Parameters
     */
    public function query($query = null, $replace = false)
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
     * @param null|array $post
     * @param bool $replace
     *
     * @return $this|Parameters
     */
    public function data($post = null, $replace = false)
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
     * @param null|array $headers
     * @param false $replace
     *
     * @return $this|Headers
     */
    public function headers($headers = null, $replace = false)
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
     * @param float|int $seconds
     *
     * @return $this
     */
    public function timeout($seconds)
    {
        $this->options['timeout'] = $seconds;

        return $this;
    }

    /**
     * @param int $milliseconds
     *
     * @return $this
     */
    public function timeoutMilliseconds($milliseconds)
    {
        $seconds = $milliseconds / 1000;

        return $this->timeout($seconds);
    }

    /**
     * @param float|int $seconds
     *
     * @return $this
     */
    public function connectTimeout($seconds)
    {
        $this->options['connect_timeout'] = $seconds;

        return $this;
    }

    /**
     * @param int $milliseconds
     *
     * @return $this
     */
    public function connectTimeoutMilliseconds($milliseconds)
    {
        $seconds = $milliseconds / 1000;

        return $this->connectTimeout($seconds);
    }

    /**
     * @param bool $debug
     *
     * @return $this
     */
    public function debug($debug)
    {
        $this->options['debug'] = $debug;

        return $this;
    }

    /**
     * @codeCoverageIgnore
     *
     * @param array $cert
     *
     * @return $this
     */
    public function cert($cert)
    {
        $this->options['cert'] = $cert;

        return $this;
    }

    /**
     * @codeCoverageIgnore
     *
     * @param array|string $proxy
     *
     * @return $this
     */
    public function proxy($proxy)
    {
        $this->options['proxy'] = $proxy;

        return $this;
    }

    /**
     * @param mixed $verify
     *
     * @return $this
     */
    public function verify($verify)
    {
        $this->options['verify'] = $verify;

        return $this;
    }

    /**
     * @return $this
     */
    public function options(array $options)
    {
        if ([] !== $options) {
            $this->options = array_merge($this->options, $options);
        }

        return $this;
    }

    /**
     * @return array
     */
    public function getOptions()
    {
        return $this->options;
    }
}
