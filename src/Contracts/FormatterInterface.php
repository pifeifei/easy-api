<?php

namespace Pff\EasyApi\Contracts;

use Pff\EasyApi\Clients\Client;
use Pff\EasyApi\Exception\ClientException;

interface FormatterInterface
{
//    /**
//     * 请求体
//     * @return mixed
//     */
//    public static function body(Client $client);
//
//    /**
//     * queryString 处理
//     * @return mixed
//     */
//    public static function query(Client $client);
//
//    /**
//     * 加签名
//     * @return void
//     * @throws ClientException
//     */
//    public static function sign(Client $client);
//
//    public static function token(Client $client);

    /**
     * 解析参数
     * @return void
     * @throws ClientException
     */
    public function resolve();
}
