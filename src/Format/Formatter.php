<?php

namespace Pff\EasyApi\Format;

use GuzzleHttp\RequestOptions;
use Pff\EasyApi\API;
use Pff\EasyApi\Clients\Client;
use Pff\EasyApi\Contracts\FormatterInterface;
use Pff\EasyApi\Exception\ClientException;
use Pff\EasyApi\Exception\ServerException;
use Symfony\Component\Serializer\Encoder\XmlEncoder;

class Formatter implements FormatterInterface
{
    /**
     * @var Client
     */
    protected $client;

    public function __construct(Client $client)
    {
        $this->client = $client;
    }

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

    protected function body()
    {
        $data = $this->client->data();
        $data->add([
            'app_id' => $this->client->config()->client('app_key'),
            'app_secret' => $this->client->config()->client('app_secret'),
            'token' => $this->client->config()->client('token'),
        ]);
        $data = $data->all();
        ksort($data);

        $method = $this->client->method();
        switch ($method) {
            case API::METHOD_POST:
                $this->client->options([RequestOptions::FORM_PARAMS => $data]);
                return;

            case API::METHOD_XML:
                $this->client->options([RequestOptions::BODY => $this->bodyXML()]);
                return;

            case API::METHOD_JSON:
                $this->client->options([RequestOptions::JSON => $data]);
                return;
            case API::METHOD_GET:
                return;
        };

        throw new ClientException('不支持的请求类型：' . $method);
    }

    /**
     * @inheritDoc
     */
    protected function query()
    {
        if ($queries = $this->client->query()->all()) {
            ksort($queries);
            $this->client->options([RequestOptions::QUERY => $queries]);
        }
    }

    /**
     * 加签名
     * @throws ClientException
     */
    protected function sign()
    {
        $signPosition = $this->client->config()->request('sign.position');
        switch ($signPosition) {
            case API::SIGN_POSITION_HEAD:
                $this->signHead();
                return;

            case API::SIGN_POSITION_GET:
                $this->signGet();
                return;

            case API::SIGN_POSITION_POST:
                $this->signPost();
                return;
            Case API::SIGN_POSITION_NONE:
                return;
        };

        throw new ClientException('不支持的加签方式');
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
            throw new ServerException($response->get('message'));
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
     * @return Client
     */
    protected function getAuthClient()
    {
        $class = get_class($this->client);
//        if ($this->client instanceof Client) {
            $client = new $class($this->client->config()->all());
            $client->tokenClient(true);

            return $client;
//        }
//
//        throw new ClientException(sprintf('%s class does not exist.', Client::class));
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
        throw new ClientException('无法获取 access token');
    }

    protected function cacheKey()
    {
        return 'access_token_' . $this->client->config()->client('appid');
    }

    protected function bodyXML()
    {
        $data = $this->client->data();
        $xml = new XmlEncoder();
        return $xml->encode($data, 'xml');
    }

    protected function signBuild()
    {
        return $this->client->getSignature()->sign(
            $this->signString($this->client->query()->all(), $this->client->data()->all()),
            $this->client->config()->client('app_secret', '')
        );
    }

    protected function signHead()
    {
        $signConfig = $this->client->getSignConfig();

        $headers = $signConfig->getAppends();
        $headers[$signConfig->getKey()] = $this->signBuild();
        $this->client->headers($headers);

        $this->client->options([RequestOptions::HEADERS => $this->client->headers()->all()]);
    }

    protected function signGet()
    {
        $signConfig = $this->client->getSignConfig();

        $queries = $signConfig->getAppends();
        $queries[$signConfig->getKey()] = $this->signBuild();
        $this->client->query($queries);
    }

    protected function signPost()
    {
        $signConfig = $this->client->getSignConfig();

        $data = $signConfig->getAppends();
        $data[$signConfig->getKey()] = $this->signBuild();
        $this->client->data($data);
    }

    protected function signString($query, $data)
    {
        $arr = array_merge($query, $data);
        return http_build_query($arr);
    }
}
