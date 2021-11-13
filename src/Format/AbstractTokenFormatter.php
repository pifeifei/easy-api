<?php

namespace Pff\EasyApi\Format;

use GuzzleHttp\RequestOptions;
use Pff\EasyApi\Clients\Client;
use Pff\EasyApi\Exception\ClientException;

abstract class AbstractTokenFormatter extends AbstractFormatter
{
    /**
     * 自动添加 token 参数
     * @throws ClientException
     */
    abstract protected function token();

    /**
     * @return Client
     */
    protected function getAuthClient()
    {
        $class = get_class($this->client);
        /* @var Client $client */
        $client = new $class($this->client->config()->all());
        $client->tokenClient(true);
        $client->options($this->client->getOptions());

        return $client;
    }

    /**
     * 获取 token 缓存名
     * @return string
     */
    protected function cacheKey()
    {
        return 'access_token_' . $this->client->config()->client('app_id');
    }
}
