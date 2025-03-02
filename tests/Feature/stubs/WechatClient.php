<?php

declare(strict_types=1);

namespace Pff\EasyApiTest\Feature\stubs;

use Doctrine\Common\Annotations\Annotation\IgnoreAnnotation;
use Pff\EasyApi\API;
use Pff\EasyApi\Cache\Cache;
use Pff\EasyApi\Clients\Client;
use Pff\EasyApi\Exception\ClientException;
use Pff\EasyApi\Exception\ServerException;
use Pff\EasyApi\Format\WechatFormatter;
use Pff\EasyApi\Result;
use Pff\EasyApi\Signature\MD5Signature;

/**
 * @IgnoreAnnotation
 *
 * @codeCoverageIgnore
 */
class WechatClient
{
    protected Client $client;

    /**
     * @var array<string, mixed>
     */
    protected array $defaultRequest = [
        'uri' => 'https://api.weixin.qq.com/cgi-bin/',
        'method' => 'POST', // 默认请求方式，可选：GET, POST, JSON
        'sign' => [
            'position' => API::SIGN_POSITION_HEAD,
            'key' => 'sign',
            'appends' => [
                'sign_type' => 'MD5',
            ],
        ],
        'signature' => MD5Signature::class, // 继承 \Pff\EasyApi\Signature\SignatureInterface::class
        'formatter' => WechatFormatter::class,
        'cache' => Cache::class, // auth 获取 access_token 等数据后，保存数据时会用到
        'format' => 'json', // 响应信息格式化
    ];

    /**
     * @param array<string, mixed> $config
     * @param ?array<string, mixed> $request
     *
     * @throws ClientException
     */
    public function __construct(array $config, ?array $request = null)
    {
        if (null === $request) {
            $this->client = new Client(['config' => $config, 'request' => $this->defaultRequest]);
        } else {
            $this->client = new Client(compact('config', 'request'));
        }

        $this->client->proxy('http://127.0.0.1:8888');
    }

    public function client(): Client
    {
        return $this->client;
    }

    /**
     * 站点绑定。
     */
    public function bind(): void
    {
        echo $_GET['echostr'] ?? ''; // @phpstan-ignore-line
    }

    /**
     * @return array<string>
     */
    public function accessToken(bool $refresh = false): array
    {
        return ['TODO: format可以从获取token'];
    }

    /**
     * 获取用户列表.
     *
     * @throws ClientException
     * @throws ServerException
     */
    public function users(?string $nextOpenid = null): Result
    {
        return $this->client()
            ->setMethod(API::METHOD_GET)
            ->path('user/get')
            ->request()
        ;
    }

    /**
     * 更新粉丝备注。
     *
     * @throws ClientException
     * @throws ServerException
     */
    public function userInfoUpdateRemark(string $openid, string $remark): Result
    {
        return $this->client()
            ->setMethod(API::METHOD_JSON)
            ->path('user/info/updateremark')
            ->setData(['openid' => $openid, 'remark' => $remark])
            ->request()
        ;
    }

    /**
     * @throws ClientException
     * @throws ServerException
     */
    public function userInfo(string $openid, ?string $lang = null): Result
    {
        return $this->client()
            ->setMethod(API::METHOD_GET)
            ->path('user/info')
            ->setQuery(compact('openid', 'lang'))
            ->request()
        ;
    }
}
