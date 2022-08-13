<?php

declare(strict_types=1);

namespace Pff\EasyApi\Exception\Contracts;

/**
 * ContextTrait.
 */
trait ContextTrait
{
    /**
     * @var array<string, mixed>
     */
    protected $context = [];

    /**
     * 获取异常上下文信息一边定位异常。
     *
     * @return array<string, mixed>
     */
    public function context(): array
    {
        if ($t = $this->getPrevious()) {
            $this->context['exception'] = $t;
        }

        return $this->context;
    }

    /**
     * 设置异常信息上下文信息。
     *
     * @param array<string, mixed> $context
     */
    public function setContext(array $context): void
    {
        $this->context = $context;
    }

    /**
     * 合并异常信息。
     *
     * @param array<string, mixed> $context
     */
    public function mergeContext(array $context): void
    {
        $this->context = array_merge($this->context, $context);
    }
}
