<?php

declare(strict_types=1);

namespace Pff\EasyApi\Format;

use GuzzleHttp\RequestOptions;
use Pff\EasyApi\API;
use Pff\EasyApi\Clients\Client;
use Pff\EasyApi\Contracts\FormatterInterface;
use Pff\EasyApi\Exception\ClientException;
use Symfony\Component\Serializer\Encoder\XmlEncoder;

abstract class AbstractFormatter implements FormatterInterface
{
    /**
     * @var Client
     */
    protected Client $client;

    public function __construct(Client $client)
    {
        $this->client = $client;
    }

    /**
     * post 数据处理。
     *
     * @return array<string, mixed>
     */
    abstract protected function getData(): array;

    /**
     * query 数据处理。
     *
     * @return array<float|int|string>|false
     */
    abstract protected function getQuery();

    /**
     * 设置 post 数据。
     *
     * @throws ClientException
     */
    protected function body(): void
    {
        $data = $this->getData();
        $this->client->options([RequestOptions::HEADERS => $this->client->headers()->all()]);
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
        }

        throw new ClientException('不支持的请求类型：' . $method, [], API::ERROR_CLIENT_UNSUPPORTED_METHOD);
    }

    /**
     * 设置 query 参数。
     */
    protected function query(): void
    {
        if ($queries = $this->getQuery()) {
            $this->client->options([RequestOptions::QUERY => $queries]);
        }
    }

    /**
     * 添加签名。
     *
     * @throws ClientException
     */
    protected function sign(): void
    {
        $signPosition = $this->client->config()->request('sign.position');

        if (empty($signPosition)) {
            return;
        }

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

            case API::SIGN_POSITION_NONE:
                return;
        }

        throw new ClientException('Unsupported signature method.', ['sign_position' => $signPosition], API::ERROR_CLIENT_UNSUPPORTED_SIGNATURE);
    }

    /**
     * 格式化 xml 请求体。
     *
     * @return false|string
     */
    protected function bodyXML()
    {
        $data = $this->client->data();
        $xml = new XmlEncoder();

        return $xml->encode($data, 'xml');
    }

    /**
     * 计算签名字符串。
     *
     * @throws ClientException
     */
    protected function signBuild(): string
    {
        throw new ClientException('Formatter does not implement signBuild method.');
    }

    /**
     * 签名放到请求头。
     *
     * @throws ClientException
     */
    protected function signHead(): void
    {
        $signConfig = $this->client->getSignConfig();

        $headers = $signConfig->getAppends();
        $headers[$signConfig->getKey()] = $this->signBuild();
        $this->client->headers($headers);
    }

    /**
     * query 方式请求签名。
     *
     * @throws ClientException
     */
    protected function signGet(): void
    {
        $signConfig = $this->client->getSignConfig();

        $queries = $signConfig->getAppends();
        $queries[$signConfig->getKey()] = $this->signBuild();
        $this->client->query($queries);
    }

    /**
     * post 中进行签名。
     *
     * @throws ClientException
     */
    protected function signPost(): void
    {
        $signConfig = $this->client->getSignConfig();

        $data = $signConfig->getAppends();
        $data[$signConfig->getKey()] = $this->signBuild();
        $this->client->data($data);
    }
}
