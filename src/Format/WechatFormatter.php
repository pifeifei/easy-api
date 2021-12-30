<?php

namespace Pff\EasyApi\Format;

use Pff\EasyApi\API;
use Pff\EasyApi\Exception\ClientException;
use Pff\EasyApi\Exception\ServerException;
use Pff\EasyApi\Utils;

/**
 * 这里微信 api 仅作案例使用，如果需要可以用稳定更新的  easy wechat.
 * 哈哈，如果有时间，也许我会完善这个案例，
 */
class WechatFormatter extends AbstractTokenFormatter
{
    /**
     * @inheritDoc
     */
    public function resolve()
    {
        if (false === $this->client->tokenClient()) {
            $this->token();
            $this->sign();
        }
        $this->query();
        $this->body();
    }

    /**
     * @inheritDoc
     */
    protected function getData(): array
    {
        $data = $this->client->data();
        $data->add([
            'app_id' => $this->client->config()->client('app_id'),
//            'app_secret' => $this->client->config()->client('app_secret'),
//            'token' => $this->client->config()->client('token'),
        ]);
        $data = $data->all();
        Utils::ksortRecursive($data);
        return $data;
    }

    /**
     * @inheritDoc
     */
    protected function getQuery()
    {
        return $this->client->query()->all();
    }

    /**
     * @inheritDoc
     */
    protected function token()
    {
        $this->client->query(['access_token' => $this->getAccessToken()]);
    }

    /**
     * @return string access token string
     * @throws ClientException
     */
    protected function getAccessToken(): string
    {
        $tokenInfo = $this->getToken();
        if (isset($tokenInfo['access_token'])) {
            return $tokenInfo['access_token'];
        }
        dump($tokenInfo);

        throw new ClientException('无法获取 access token', API::ERROR_CLIENT_FAILED_GET_ACCESS);
    }

    /**
     * 获取 access token
     * @param bool $refresh
     *
     * @return array 请求的 token 完整数据
     *
     * @throws ClientException
     * @throws \Psr\SimpleCache\InvalidArgumentException
     * @throws ServerException
     */
    protected function getToken(bool $refresh = false)
    {
        $key = $this->cacheKey();
        $cache = $this->client->cache();
        if ($cache->has($key)) {
            $tokenInfo = $cache->get($key);
            if ($tokenInfo['expired_at'] <= time()) {
                $config = $this->client->config();
                $query = [
                    'appid' => $config->client('app_id'),
                    'secret' => $config->client('app_secret'),
                    'grant_type' => 'client_credential',
                ];
                $authClient = $this->getAuthClient();
                $response = $authClient
                    ->path('token')
                    ->method('GET')
                    ->query($query)
                    ->request();

                $tokenInfo = [
                    'access_token' => $response->get('access_token'),
                    'expired_at' => time() + $response->get('expires_in'),
                ];
                $cache->set($key, $tokenInfo, $response->get('expires_in'));
            }

            return $tokenInfo;
        }

        $config = $this->client->config();
        $query = [
            'appid' => $config->client('app_id'),
            'secret' => $config->client('app_secret'),
            'grant_type' => 'client_credential',
        ];
        $authClient = $this->getAuthClient();
        $response = $authClient
            ->path('token')
            ->method('GET')
            ->query($query)
            ->request();

        if ($response->has('errcode') && 0 !== $response->get('errcode')) {
            throw new ServerException($response, $response->get('errmsg'), $response->getStatusCode());
        }

        $tokenInfo = [
            'access_token' => $response->get('access_token'),
            'expired_at' => time() + $response->get('expires_in'),
        ];
        $cache->set($key, $tokenInfo, $response->get('expires_in'));

        return $tokenInfo;
    }

    /**
     * @inheritDoc
     */
    protected function signBuild(): string
    {
        return '';
    }
}
