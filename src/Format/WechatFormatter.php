<?php

declare(strict_types=1);

namespace Pff\EasyApi\Format;

use Pff\EasyApi\API;
use Pff\EasyApi\Exception\ClientException;
use Pff\EasyApi\Exception\ServerException;
use Pff\EasyApi\Utils;

/**
 * 这里微信 api 仅作案例使用，如果需要可以用稳定更新的  easy wechat.
 * 哈哈，如果有时间，也许我会完善这个案例，.
 */
class WechatFormatter extends AbstractTokenFormatter
{
    /**
     * {@inheritDoc}
     */
    public function resolve(): void
    {
        if (false === $this->client->isTokenClient()) {
            $this->token();
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
            'app_id' => $this->client->config()->client('app_id'),
        ]);
        $data = $data->all();
        Utils::ksortRecursive($data);

        return $data;
    }

    /**
     * {@inheritDoc}
     */
    protected function getQuery()
    {
        return $this->client->getQuery()->all();
    }

    /**
     * {@inheritDoc}
     */
    protected function token(): void
    {
        $this->client->setQuery(['access_token' => $this->getAccessToken()]);
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

        throw new ClientException('无法获取 access token', [], API::ERROR_CLIENT_FAILED_GET_ACCESS);
    }

    /**
     * 获取 access token.
     *
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
                    'appid' => $config->client('app_id'),
                    'secret' => $config->client('app_secret'),
                    'grant_type' => 'client_credential',
                ];
                $authClient = $this->getAuthClient();
                $response = $authClient
                    ->path('token')
                    ->setMethod('GET')
                    ->setQuery($query)
                    ->request()
                ;

                /** @var array<string, string> $tokenInfo */
                $tokenInfo = [
                    'access_token' => $response->get('access_token'),
                    'expired_at' => time() + $response->get('expires_in'),
                ];
                $cache->set($key, $tokenInfo, $response->get('expires_in'));  // @phpstan-ignore-line
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
            ->setMethod('GET')
            ->setQuery($query)
            ->request()
        ;

        if ($response->has('errcode') && 0 !== $response->get('errcode')) {
            throw new ServerException(
                $response,
                'api error: ' . $response->get('errmsg'),
                ['appid' => $config->client('app_id')],
                $response->getStatusCode()
            );
        }

        /** @var array<string, string> $tokenInfo */
        $tokenInfo = [
            'access_token' => $response->get('access_token'),
            'expired_at' => time() + $response->get('expires_in'),
        ];
        $cache->set($key, $tokenInfo, $response->get('expires_in')); // @phpstan-ignore-line

        return $tokenInfo;
    }

    /**
     * {@inheritDoc}
     */
    protected function signBuild(): string
    {
        return '';
    }
}
