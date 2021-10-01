<?php

namespace Pff\EasyApi\Format;

use GuzzleHttp\RequestOptions;
use Pff\EasyApi\API;
use Pff\EasyApi\Clients\Client;
use Pff\EasyApi\Exception\ClientException;
use Pff\EasyApi\Exception\ServerException;

class Formatter extends AbstractTokenFormatter
{
    /**
     * @inheritDoc
     */
    public function resolve()
    {
        if (false === $this->client->tokenClient()) {
            method_exists($this, 'token') && $this->token();
            $this->sign();
        }
        $this->query();
        $this->body();
    }

    /**
     *
     * @return array
     */
    protected function getData()
    {
        $data = $this->client->data();
        $data->add([
            'app_id' => $this->client->config()->client('app_key'),
            'app_secret' => $this->client->config()->client('app_secret'),
            'token' => $this->client->config()->client('token'),
        ]);
        $data = $data->all();
        ksort($data);
        return $data;
    }

    /**
     * @return array|false
     */
    protected function getQuery()
    {
        if ($queries = $this->client->query()->all()) {
            ksort($queries);
            return $queries;
        }

        return false;
    }

    /**
     * @throws ClientException
     */
    protected function token()
    {
        $this->client->query(['access_token' => $this->getAccessToken()]);
    }

    /**
     * @param bool $refresh
     *
     * @return array 请求的 token 完整数据
     *
     * @throws ClientException
     * @throws \Psr\SimpleCache\InvalidArgumentException
     * @throws ServerException
     */
    protected function getToken($refresh = false)
    {
        $key = $this->cacheKey();
        $cache = $this->client->cache();
        if ($cache->has($key)) {
            $tokenInfo = $cache->get($key);
            if ($tokenInfo['expired_at'] <= time()) {
                $config = $this->client->config();
                $query = [
                    'app_id' => $config->client('app_id'),
                    'app_secret' => $config->client('app_secret'),
                    'grant_type' => 'refresh_token',
                    'refresh_token' => $tokenInfo['refresh_token']
                ];
                $authClient = $this->getAuthClient();
                $response = $authClient
                    ->path('token/refresh')
                    ->method('GET')
                    ->query($query)
                    ->request();

                $tokenInfo = [
                    'access_token' => $response['data']['access_token'],
                    'expired_at' => time() + $response['data']['expires_in'],
                    'refresh_token' => $response['data']['refresh_token']
                ];
                $cache->set($key, $tokenInfo, 86400*14);
            }

            return $tokenInfo;
        }

        $config = $this->client->config();
        $query = [
            'app_id' => $config->client('app_id'),
            'app_secret' => $config->client('app_secret'),
            'grant_type' => 'authorization_self',
            'shop_id' => $config->client('shop_id'),
        ];
        $authClient = $this->getAuthClient();
        $response = $authClient
            ->path('create/token')
            ->method('GET')
            ->query($query)
            ->request();

        if (0 !== $response->get('err_no')) {
            throw new ServerException($response->get('message'), $response->getStatusCode());
        }

        $tokenInfo = [
            'access_token' => $response['data']['access_token'],
            'expired_at' => time() + $response['data']['expires_in'],
            'refresh_token' => $response['data']['refresh_token']
        ];
        $cache->set($key, $tokenInfo, 86400*14);

        return $tokenInfo;
    }

    /**
     * @return string access token string
     * @throws ClientException
     */
    protected function getAccessToken()
    {
        $tokenInfo = $this->getToken();
        if (isset($tokenInfo['access_token'])) {
            return $tokenInfo['access_token'];
        }
        throw new ClientException('无法获取 access token', API::ERROR_CLIENT_FAILED_GET_ACCESS);
    }

    /**
     * @inheritDoc
     *
     * @return string
     */
    protected function signBuild()
    {
        return $this->client->getSignature()->sign(
            $this->signString($this->client->query()->all(), $this->client->data()->all()),
            $this->client->config()->client('app_secret', '')
        );
    }

    /**
     * 生成待签名的字符串
     *
     * @param array $query
     * @param array $data
     * @return string
     */
    protected function signString($query, $data)
    {
        $arr = array_merge($query, $data);
        return http_build_query($arr);
    }
}
