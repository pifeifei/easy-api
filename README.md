# easy api client

一个容易上手的 api 客户端工具

## Requirement

1. PHP >= 7.4
2. Composer
3. openssl 拓展（建议）
4. fileinfo 拓展（建议）

## Installation

```shell
composer require "pifeifei/easy-api"
```

## Usage

```php
$config = [
    'config' => [
        'app_key' => 'app key string',
        'app_secret' => 'app secret string'
    ],
    'request' => [
        "uri" => 'https://httpbin.org/anything',
        "sandbox_uri" => 'https://httpbin.org/anything/sandbox',
        "method" => Pff\EasyApi\API::METHOD_JSON, // 默认请求方式，可选：GET, POST, JSON
        "sign" => [
            "position" => Pff\EasyApi\API::SIGN_POSITION_HEAD,
            "key" => "sign",
            "appends" => [
                "sign_type" => 'MD5'
            ],
        ],
        "signature" => Pff\EasyApi\Signature\MD5Signature::class, // 继承 \Pff\EasyApi\Signature\SignatureInterface::class
        "formatter" => Pff\EasyApi\Format\HttpBinFormatter::class,
        "cache" => Pff\EasyApi\Cache\Cache::class, // auth 获取 access_token 等数据后，保存数据时会用到
        "format" => Pff\EasyApi\API::RESPONSE_FORMAT_JSON, // 响应信息格式化
    ]
];

$client = new Client($config);
$response = $client->request();
// get
$response = $client->method(Pff\EasyApi\API::METHOD_GET)
    ->path("any/path/to")
    ->setData(['a'=>'foo', 'b'=>'bar'])
    ->request();
// json post
$response = $client->path("any/path/to")
    ->setData(['a'=>'foo', 'b'=>'bar'])
    ->request();
```

> 把上面代码封装一下会更好用，可以参考 tests/Feature/Clients/WechatClient


## test

```shell
phpunit
# phpunit --list-groups

# 微信开放平台测试
#php -S 0.0.0.0:8080 -t ./tests/stubs/wechat
#phpunit --group wechat
```
