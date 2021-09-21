<?php

use Pff\EasyApi\API;

return [
    'default' => 'wechat1', // configures 中的键名

    'clients' => [
        'wechat1' => [
            'config' => [
                'appid' => 'wx19b0da5c7edf2b22',
                'appsecret' => '1c1972c00626709d5b99d8c642f1affa',
                'token' => 'b3o3R0Y400H752hiI2weD4W2fH0REehO',
            ],
            'request' => 'wechat',
        ]
    ],

    'requests' => [
        'wechat' => [
             "uri" => 'https://api.weixin.qq.com/api/',
//            "uri" => [
//                "host" => "api.weixin.qq.com",
//                // "port" => "80",
//                "scheme" => "https", // http, https
////                "prefix" => "/cgi-bin/", // 前缀, 以 / 结尾， 每次访问都会附加
//            ],
            "method" => "GET", // 默认请求方式，可选：GET, POST, JSON

            "sign" => [
                "position" => API::SIGN_POSITION_HEAD,
                "key" => "sign",
                "appends" => [
                    "sign_type" => 'MD5'
                ],
            ],
//            "adaptor" => \Pff\EasyApi\Adaptor\Adaptor::class, // 适配器
            "signature" => \Pff\EasyApi\Signature\MD5Signature::class, // 继承 \Pff\EasyApi\Signature\SignatureInterface::class
            "formatter" => Pff\EasyApi\Format\Formatter::class,
//            "formats" => [
//              "data" => Pff\EasyApi\Formats\Formatter::class,
//              "query" => Pff\EasyApi\Formats\QueryFormat::class,
//              "sign" => Pff\EasyApi\Formats\Formatter::class,
//            ],
//            "config" => \Pff\EasyApi\Config::class, // 参数弃用
            "cache" => \Pff\EasyApi\Cache\Cache::class, // auth 获取 access_token 等数据后，保存数据时会用到
            "format" => "json", // 响应信息格式化
        ],

        'wechat-pay' => [

        ],
        'alipay' => [

        ]
    ]
];
