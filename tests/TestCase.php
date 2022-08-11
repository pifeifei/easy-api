<?php

declare(strict_types=1);

namespace Pff\EasyApiTest;

use Pff\EasyApi\API;
use Pff\EasyApi\Cache\Cache;
use Pff\EasyApi\Format\HttpBinFormatter;
use Pff\EasyApi\Signature\MD5Signature;
use PHPUnit\Framework\TestCase as BaseTestCase;

/**
 * @internal
 * @coversNothing
 */
class TestCase extends BaseTestCase
{
    protected function getConfig()
    {
        return [
            'config' => [
                'app_key' => 'app key string',
                'app_secret' => 'app secret string',
            ],
            'request' => [
                'uri' => 'https://httpbin.org/anything',
                'method' => API::METHOD_JSON, // 默认请求方式，可选：GET, POST, JSON
                'sign' => [
                    'position' => API::SIGN_POSITION_HEAD,
                    'key' => 'sign',
                    'appends' => [
                        'sign_type' => 'MD5',
                    ],
                ],
                'signature' => MD5Signature::class, // 继承 \Pff\EasyApi\Signature\SignatureInterface::class
                'formatter' => HttpBinFormatter::class,
                'cache' => Cache::class, // auth 获取 access_token 等数据后，保存数据时会用到
                'format' => API::RESPONSE_FORMAT_JSON, // 响应信息格式化
            ],
        ];
    }
}
