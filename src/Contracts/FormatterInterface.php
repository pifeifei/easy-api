<?php

namespace Pff\EasyApi\Contracts;

use Pff\EasyApi\Exception\ClientException;

interface FormatterInterface
{
    /**
     * 解析参数
     * @return void
     * @throws ClientException
     */
    public function resolve();
}
