<?php

declare(strict_types=1);

namespace Pff\EasyApiTest\Feature;

use Pff\EasyApi\API;
use Pff\EasyApi\Cache\Cache;
use Pff\EasyApi\Format\WechatFormatter;
use Pff\EasyApiTest\Feature\Clients\WechatClient;
use Pff\EasyApiTest\TestCase;

/**
 * @group wechat
 *
 * @internal
 * @coversNothing
 */
final class WechatTest extends TestCase
{
    public function getConfig()
    {
        if (empty(getenv('WECHAT_APP_ID'))) {
            throw new \InvalidArgumentException(
                'Please set the environment variable: WECHAT_APP_ID. WECHAT_APP_SECRET, WECHAT_TOKEN.'
            );
        }

        return [
            //            'config' => [
            'app_id' => getenv('WECHAT_APP_ID'),
            'app_secret' => getenv('WECHAT_APP_SECRET'),
            'token' => getenv('WECHAT_TOKEN'),
            //            ],
            //            'request' => [
            //                "uri" => 'https://api.weixin.qq.com/cgi-bin/',
            //                "method" => API::METHOD_JSON, // 默认请求方式，可选：GET, POST, JSON
            //                "formatter" => WechatFormatter::class,
            //                "cache" => Cache::class, // auth 获取 access_token 等数据后，保存数据时会用到
            //                "format" => API::RESPONSE_FORMAT_JSON, // 响应信息格式化
            //            ]
        ];
    }

    public function testUsers(): void
    {
        $config = $this->getConfig();
        $client = new WechatClient($config);
        $response = $client->users();
        static::assertTrue($response->has('total'));

        if ($response->has('data.openid')) {
            $result = $client->userInfo($response->get('data.openid')[0]);
            static::assertTrue($result->has('openid'));
            static::assertSame($result->get('openid'), $response->get('data.openid')[0]);
        }
    }
}
