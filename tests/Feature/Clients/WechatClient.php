<?php

namespace Pff\EasyApiTest\Feature\Clients;

use Pff\EasyApi\API;
use Pff\EasyApi\Cache\Cache;
use Pff\EasyApi\Clients\Client;
use Pff\EasyApi\Exception\ClientException;
use Pff\EasyApi\Exception\ServerException;
use Pff\EasyApi\Format\WechatFormatter;
use Pff\EasyApi\Result;
use Pff\EasyApi\Signature\MD5Signature;

class WechatClient
{
    protected $client;

    protected $defaultRequest = [
        "uri" => 'https://api.weixin.qq.com/cgi-bin/',
        "method" => "POST", // 默认请求方式，可选：GET, POST, JSON
        "sign" => [
            "position" => API::SIGN_POSITION_HEAD,
            "key" => "sign",
            "appends" => [
                "sign_type" => 'MD5'
            ],
        ],
        "signature" => MD5Signature::class, // 继承 \Pff\EasyApi\Signature\SignatureInterface::class
        "formatter" => WechatFormatter::class,
        "cache" => Cache::class, // auth 获取 access_token 等数据后，保存数据时会用到
        "format" => "json", // 响应信息格式化
    ];

    /**
     * @param array $config
     * @param ?array $request
     */
    public function __construct(array $config, array $request = null)
    {
        if (is_null($request)) {
            $this->client = new Client(['config' => $config, 'request' => $this->defaultRequest]);
        } else {
            $this->client = new Client(compact('config', 'request'));
        }

        $this->client->proxy( 'http://127.0.0.1:8888');
    }

    public function client()
    {
        return $this->client;
    }

    /**
     * 站点绑定
     */
    public function bind()
    {
        echo $_GET['echostr'] ?? '';
    }

    /**
     * @return array
     */
    public function accessToken($refresh = false)
    {
        return ['TODO: format可以从获取token'];
    }

    /**
     * 获取用户列表
     * @param string|null $nextOpenid
     * @return Result
     * @throws ClientException
     * @throws ServerException
     */
    public function users($nextOpenid = null)
    {
        return $this->client()
            ->method(API::METHOD_GET)
            ->path('user/get')
//            ->data(['next_openid' => $nextOpenid])
            ->request()
        ;
    }

    /**
     * @param $openid
     * @param $remark
     * @return Result
     * @throws ClientException
     * @throws ServerException
     */
    public function userInfoUpdateRemark($openid, $remark)
    {
        return $this->client()
            ->method(API::METHOD_JSON)
            ->path('user/info/updateremark')
            ->data(['openid' => $openid, 'remark' => $remark])
            ->request();
    }

    /**
     * @param $openid
     * @param null $lang
     * @return Result
     * @throws ClientException
     * @throws ServerException
     */
    public function userInfo($openid, $lang = null)
    {
        return $this->client()
            ->method(API::METHOD_GET)
            ->path('user/info')
            ->query(compact('openid', 'lang'))
            ->request();
    }
}
