<?php

declare(strict_types=1);

namespace Pff\EasyApi\Concerns;

use Psr\Http\Message\UriInterface;

trait UriTrait
{
    protected UriInterface $uri;

    protected string $prefixPath;

    public function uri(): UriInterface
    {
        return $this->uri;
    }

    /**
     * @return $this
     */
    public function scheme(string $scheme): self
    {
        $this->uri = $this->uri()->withScheme($scheme);

        return $this;
    }

    /**
     * @return $this
     */
    public function host(string $host): self
    {
        $this->uri = $this->uri()->withHost($host);

        return $this;
    }

    /**
     * @return $this
     */
    public function path(string $path): self
    {
        if (0 === strpos($path, '/')) {
            $this->uri = $this->uri()->withPath($path);
        } else {
            $this->uri = $this->uri()->withPath($this->prefixPath . '/' . $path);
        }

        return $this;
    }

    /**
     * @return $this
     */
    public function port(int $port): self
    {
        $this->uri = $this->uri()->withPort($port);

        return $this;
    }
}
