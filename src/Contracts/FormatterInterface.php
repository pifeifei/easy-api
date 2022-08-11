<?php

declare(strict_types=1);

namespace Pff\EasyApi\Contracts;

use Pff\EasyApi\Exception\ClientException;

interface FormatterInterface
{
    /**
     * 解析参数.
     *
     * @throws ClientException
     */
    public function resolve(): void;
}
