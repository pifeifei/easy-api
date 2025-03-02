<?php

declare(strict_types=1);

namespace Pff\EasyApi\Request;

use Illuminate\Support\Arr;
use Pff\EasyApi\API;

class SignConfig
{
    protected string $key;

    protected string $position = API::SIGN_POSITION_HEAD;

    /**
     * @var array<string, string>
     */
    protected $appends = [];

    /**
     * @param array<string, string> $appends
     */
    final public function __construct(string $key, ?string $position = null, array $appends = [])
    {
        $this->key = $key;
        if (null !== $position) {
            $this->position = $position;
        }
        $this->appends = (array) $appends;
    }

    /**
     * @param array<string, mixed> $config
     *
     * @return static
     */
    public static function create(array $config = []): self
    {
        /** @var string $key */
        $key = Arr::get($config, 'key');

        /** @var null|string $position */
        $position = Arr::get($config, 'position');

        /** @var array<string, string> $appends */
        $appends = Arr::get($config, 'appends', []);

        return new static($key, $position, $appends);
    }

    public function getKey(): string
    {
        return $this->key;
    }

    public function setKey(string $key): self
    {
        $this->key = $key;

        return $this;
    }

    public function getPosition(): string
    {
        return $this->position;
    }

    public function setPosition(string $position): void
    {
        $this->position = $position;
    }

    /**
     * @return array|string[]
     */
    public function getAppends(): array
    {
        return $this->appends;
    }

    /**
     * @param array<string, string> $appends
     */
    public function setAppends(array $appends): void
    {
        $this->appends = $appends;
    }
}
