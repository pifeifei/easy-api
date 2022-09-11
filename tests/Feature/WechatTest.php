<?php

declare(strict_types=1);

namespace Pff\EasyApiTest\Feature;

use Pff\EasyApi\Exception\ClientException;
use Pff\EasyApi\Exception\ServerException;
use Pff\EasyApiTest\Feature\stubs\WechatClient;
use Pff\EasyApiTest\TestCase;

/**
 * @group wechat
 *
 * @internal
 *
 * @coversNothing
 */
final class WechatTest extends TestCase
{
    /**
     * @return array<string, mixed>
     */
    public function getConfig(): array
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

    /**
     * @throws ClientException
     * @throws ServerException
     */
    public function testUsers(): void
    {
        $config = $this->getConfig();
        $client = new WechatClient($config);
        $response = $client->users();
        $this->assertTrue($response->has('total'));

        if ($response->has('data.openid')) {
            $result = $client->userInfo($response->get('data.openid')[0]); // @phpstan-ignore-line
            $this->assertTrue($result->has('openid'));
            $this->assertSame($result->get('openid'), $response->get('data.openid')[0]); // @phpstan-ignore-line
        }
    }
}
