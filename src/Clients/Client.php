<?php

declare(strict_types=1);

namespace Pff\EasyApi\Clients;

use GuzzleHttp\Exception\GuzzleException;
use GuzzleHttp\Psr7\Uri;
use GuzzleHttp\RequestOptions;
use Pff\EasyApi\API;
use Pff\EasyApi\Concerns\CacheTrait;
use Pff\EasyApi\Concerns\Client\HistoryTrait;
use Pff\EasyApi\Concerns\Client\MockTrait;
use Pff\EasyApi\Concerns\Client\RetryTrait;
use Pff\EasyApi\Concerns\ClientTrait;
use Pff\EasyApi\Concerns\HttpTrait;
use Pff\EasyApi\Concerns\UriTrait;
use Pff\EasyApi\Config;
use Pff\EasyApi\Contracts\ConfigInterface;
use Pff\EasyApi\Contracts\FormatterInterface;
use Pff\EasyApi\Exception\ClientException;
use Pff\EasyApi\Exception\ServerException;
use Pff\EasyApi\Request\Headers;
use Pff\EasyApi\Request\Parameters;
use Pff\EasyApi\Request\SignConfig;
use Pff\EasyApi\Request\UserAgent;
use Pff\EasyApi\Result;

class Client
{
    use CacheTrait;
    use ClientTrait;
    use HistoryTrait;
    use HttpTrait;
    use MockTrait;
    use RetryTrait;
    use UriTrait;

    /**
     * Request Connect Timeout.
     */
    public const CONNECT_TIMEOUT = 5;

    /**
     * Request Timeout.
     */
    public const TIMEOUT = 10;

    protected ConfigInterface $config;

    protected FormatterInterface $formatter;

    /**
     * Response format.
     */
    protected string $format;

    protected SignConfig $signConfig;

    /**
     * @var array<string, string|true>
     */
    protected $userAgent = [];

    /**
     * @param array<string, mixed> $config
     *
     * @throws ClientException
     */
    public function __construct(array $config)
    {
        $this->config = Config::create($config);
        $this->options[RequestOptions::HTTP_ERRORS] = false;
        $this->options[RequestOptions::CONNECT_TIMEOUT] = self::CONNECT_TIMEOUT;
        $this->options[RequestOptions::TIMEOUT] = self::TIMEOUT;

        $this->init();
    }

    /**
     * @return $this
     */
    public function appendUserAgent(string $name, string $value): self
    {
        if (!UserAgent::isGuarded($name)) {
            $this->userAgent[$name] = $value;
        }

        return $this;
    }

    /**
     * @param array<string, string|true> $userAgent
     *
     * @return static
     */
    public function withUserAgent(array $userAgent): self
    {
        $this->userAgent = $userAgent;

        return $this;
    }

    public function format(): string
    {
        return $this->format;
    }

    /**
     * 清空数据。
     */
    public function clear(): self
    {
        $this->method = $this->config()->requestMethod();
        $this->query->replace([]);
        $this->data->replace([]);
        $this->headers->replace([]);
        $this->cleanOptions();

        return $this;
    }

    /**
     * 获取配置对象。
     */
    public function config(): ConfigInterface
    {
        return $this->config;
    }

    public function getSignConfig(): SignConfig
    {
        return $this->signConfig;
    }

    /**
     * @throws ClientException
     */
    public function resolveOption(): void
    {
        $this->headers->set('User-Agent', UserAgent::toString($this->userAgent));
        $this->cleanOptions();
        $this->formatter->resolve();
    }

    /**
     * @throws ClientException
     * @throws ServerException
     */
    public function request(): Result
    {
        $this->resolveOption();
        $result = $this->response();

        // if ($this->shouldServerRetry($result)) {
        //     return $this->request();
        // }

        if (!$result->isSuccess()) {
            throw new ServerException(
                $result,
                \sprintf('%d %s', $result->getStatusCode(), $result->getReasonPhrase()),
                ['options' => $this->getOptions()],
                API::ERROR_SERVER_UNKNOWN
            );
        }

        return $result;
    }

    /**
     * @throws ClientException
     */
    protected function init(): void
    {
        $config = $this->config();
        $this->uri = new Uri($config->requestUri());
        $this->prefixPath = rtrim($this->uri()->getPath(), '/');
        $this->method = $config->requestMethod(API::METHOD_POST);

        $formatter = $config->requestFormatter();
        if (!class_exists($formatter)) {
            throw new \UnexpectedValueException(\sprintf('%s class does not exist.', $formatter));
        }

        $formatter = new $formatter($this);
        if (!$formatter instanceof FormatterInterface) {
            throw new \UnexpectedValueException(\sprintf('Formatter must implement %s interface.', FormatterInterface::class));
        }

        $this->formatter = $formatter;
        $this->format = $config->requestFormat();
        $this->query = new Parameters();
        $this->data = new Parameters();
        $this->headers = new Headers();

        $this->signConfig = SignConfig::create($config->requestSign());

        $this->setSignature($config->requestSignature());
        $this->setCache($config->requestCache());
    }

    /**
     * @throws ClientException
     */
    private function response(): Result
    {
        try {
            /** @var Result $result */
            $result = self::createClient($this)->request(
                $this->requestMethod(),
                (string) $this->uri,
                $this->options
            );

            /** @var Result */
            return value($result);
        } catch (GuzzleException $exception) {
            // if ($this->shouldClientRetry($exception)) {
            //     return $this->response();
            // }
            $context = [
                'method' => $this->requestMethod(),
                'uri' => (string) $this->uri,
                'options' => $this->options,
            ];

            throw new ClientException($exception->getMessage(), $context, API::ERROR_CLIENT_UNKNOWN, $exception);
        }
    }

    /**
     * Remove redundant Query.
     *
     * @codeCoverageIgnore
     */
    private function cleanOptions(): void
    {
        if (isset($this->options[RequestOptions::HEADERS])) {
            unset($this->options[RequestOptions::HEADERS]);
        }
        if (isset($this->options[RequestOptions::QUERY])) {
            unset($this->options[RequestOptions::QUERY]);
        }
        if (isset($this->options[RequestOptions::FORM_PARAMS])) {
            unset($this->options[RequestOptions::FORM_PARAMS]);
        }
        if (isset($this->options[RequestOptions::JSON])) {
            unset($this->options[RequestOptions::JSON]);
        }
    }
}
