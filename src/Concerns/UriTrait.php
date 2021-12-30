<?php

namespace Pff\EasyApi\Concerns;

use Psr\Http\Message\UriInterface;

trait UriTrait
{
    /**
     * @var UriInterface
     */
    protected $uri;

    /**
     * @var string
     */
    protected $prefixPath;

    /**
     * @return UriInterface
     */
    public function uri()
    {
        return $this->uri;
    }

    /**
     * @param string $scheme
     * @return $this
     */
    public function scheme(string $scheme)
    {
        $this->uri = $this->uri()->withScheme($scheme);
        return $this;
    }

    /**
     * @param string $host
     * @return $this
     */
    public function host(string $host)
    {
        $this->uri = $this->uri()->withHost($host);
        return $this;
    }

    /**
     * @param string $path
     * @return $this
     */
    public function path(string $path)
    {
        if (strpos($path, '/') === 0) {
            $this->uri = $this->uri()->withPath($path);
        } else {
            $this->uri = $this->uri()->withPath( $this->prefixPath . '/' .$path);
        }

        return $this;
    }

    /**
     * @param int $port
     * @return $this
     */
    public function port(int $port)
    {
        $this->uri = $this->uri()->withPort($port);
        return $this;
    }
}
