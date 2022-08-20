<?php

declare(strict_types=1);

namespace Pff\EasyApi\Format;

use Pff\EasyApi\API;
use Pff\EasyApi\Exception\ClientException;
use Pff\EasyApi\Exception\ServerException;

class Formatter extends AbstractTokenFormatter
{
    /**
     * {@inheritDoc}
     */
    public function resolve(): void
    {
        if (false === $this->client->isTokenClient()) {
            method_exists($this, 'token') && $this->token();
            $this->sign();
        }
        $this->query();
        $this->body();
    }

    /**
     * {@inheritDoc}
     */
    protected function getData(): array
    {
        $data = $this->client->getData();
        $data->add([
            'app_id' => $this->client->config()->client('app_key'),
        ]);
        $data = $data->all();
        ksort($data);

        return $data;
    }

    /**
     * {@inheritDoc}
     */
    protected function getQuery()
    {
        if ($queries = $this->client->getQuery()->all()) {
            ksort($queries);

            return $queries;
        }

        return false;
    }

    /**
     * {@inheritDoc}
     *
     * @throws ClientException
     */
    protected function token(): void
    {
        $this->client->setQuery(['access_token' => $this->getAccessToken()]);
    }

    /**
     * @throws ClientException
     * @throws ServerException
     *
     * @return array<string, string> 请求的 token 完整数据
     */
    protected function getToken(bool $refresh = false): array
    {
        $key = $this->cacheKey();
        $cache = $this->client->cache();
        if (false === $refresh && $cache->has($key)) {
            /** @var array<string, string> $tokenInfo */
            $tokenInfo = $cache->get($key);
            if ($tokenInfo['expired_at'] <= time()) {
                $config = $this->client->config();
                $query = [
                    'app_id' => $config->client('app_id'),
                    'app_secret' => $config->client('app_secret'),
                    'grant_type' => 'refresh_token',
                    'refresh_token' => $tokenInfo['refresh_token'],
                ];
                $authClient = $this->getAuthClient();
                $response = $authClient
                    ->path('token/refresh')
                    ->setMethod('GET')
                    ->setQuery($query)
                    ->request()
                ;

                /** @var array<string, string> $tokenInfo */
                $tokenInfo = [
                    'access_token' => $response->get('access_token'),
                    'expired_at' => time() + $response->get('expires_in'),
                    'refresh_token' => $response->get('refresh_token'),
                ];
                $cache->set($key, $tokenInfo, 86400 * 14);
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
            ->setMethod('GET')
            ->setQuery($query)
            ->request()
        ;

        if (0 !== $response->get('err_no')) {
            throw new ServerException(
                $response,
                'api error: ' . $response->get('message'),
                ['appid' => $config->client('app_id')],
                $response->getStatusCode()
            );
        }

        /** @var array<string, string> $tokenInfo */
        $tokenInfo = [
            'access_token' => $response->get('access_token'),
            'expired_at' => time() + $response->get('expires_in'),
            'refresh_token' => $response->get('refresh_token'),
        ];
        $cache->set($key, $tokenInfo, 86400 * 14);

        return $tokenInfo;
    }

    /**
     * @throws ClientException
     * @throws ServerException
     *
     * @return string access token string
     */
    protected function getAccessToken(): string
    {
        $tokenInfo = $this->getToken();
        if (isset($tokenInfo['access_token'])) {
            return $tokenInfo['access_token'];
        }

        throw new ClientException(
            '无法获取 access token',
            ['appid' => $this->client->config()->client('app_id')],
            API::ERROR_CLIENT_FAILED_GET_ACCESS
        );
    }

    /**
     * {@inheritDoc}
     */
    protected function signBuild(): string
    {
        return $this->client->getSignature()->sign(
            $this->signString($this->client->getQuery()->all(), $this->client->getData()->all()),
            $this->client->config()->client('app_secret', '') // @phpstan-ignore-line
        );
    }

    /**
     * 生成待签名的字符串。
     *
     * @param array<string, mixed> $query
     * @param array<string, mixed> $data
     */
    protected function signString(array $query, array $data): string
    {
        $arr = array_merge($query, $data);

        return http_build_query($arr);
    }
}
