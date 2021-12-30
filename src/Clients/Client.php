<?php

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
use UnexpectedValueException;

class Client
{
    use CacheTrait, ClientTrait, HttpTrait, HistoryTrait, MockTrait, RetryTrait, UriTrait;

    /**
     * Request Connect Timeout
     */
    const CONNECT_TIMEOUT = 5;

    /**
     * Request Timeout
     */
    const TIMEOUT = 10;

    /**
     * @var ConfigInterface
     */
    protected $config;

    /**
     * @var FormatterInterface
     */
    protected $formatter;

    /**
     * Response format
     *
     * @var string
     */
    protected $format;

    /**
     * @var SignConfig
     */
    protected $signConfig;

    /**
     * @var array
     */
    protected $userAgent = [];

    public function __construct($config)
    {
        $this->config = Config::create($config);
        $this->options[RequestOptions::HTTP_ERRORS]     = false;
        $this->options[RequestOptions::CONNECT_TIMEOUT] = self::CONNECT_TIMEOUT;
        $this->options[RequestOptions::TIMEOUT]         = self::TIMEOUT;

        $this->init();
    }

    /**
     * @param string $name
     * @param string $value
     * @return $this
     */
    public function appendUserAgent(string $name, string $value): Client
    {
        if (!UserAgent::isGuarded($name)) {
            $this->userAgent[$name] = $value;
        }

        return $this;
    }

    /**
     * @param array $userAgent
     * @return Client
     */
    public function withUserAgent(array $userAgent): Client
    {
        $this->userAgent = $userAgent;

        return $this;
    }

    /**
     * @return string
     */
    public function format(): string
    {
        return $this->format;
    }

    protected function init()
    {
        $config = $this->config();
        $this->uri = new Uri($config->request('uri'));
        $this->prefixPath = rtrim($this->uri()->getPath(), '/');
        $this->method = $config->request('method', API::METHOD_POST);

        $formatter = $config->request('formatter');
        if (! class_exists($formatter)) {
            throw new UnexpectedValueException(sprintf('%s class does not exist.', $formatter));
        }
        $this->formatter = new $formatter($this);
        if (! ($this->formatter instanceof FormatterInterface)) {
            throw new UnexpectedValueException(sprintf('Formatter must implement %s interface.', FormatterInterface::class));
        }

        $this->format = $config->request('format', API::RESPONSE_FORMAT_JSON);

        $this->query = new Parameters();
        $this->data = new Parameters();
        $this->headers = new Headers();

        $this->signConfig = SignConfig::create($config->request('sign'));

        $this->setSignature($config->request('signature'));
        $this->setCache($config->request('cache'));
    }

    /**
     * 清空数据
     */
    public function clear(): Client
    {
        $this->method = $this->config()->request('method', API::METHOD_POST);
        $this->query->replace([]);
        $this->data->replace([]);
        $this->headers->replace([]);
        $this->cleanOptions();
        return $this;
    }

    /**
     * @param null $name
     * @param null $default
     * @return ConfigInterface|array|bool|float|int|string
     */
    public function config($name = null, $default = null)
    {
        if (is_null($name)) {
            return $this->config;
        }

        if (is_array($name)) {

            return $this->config;
        }

        return $this->config->get($name, $default);
    }

    public function getSignConfig(): SignConfig
    {
        return $this->signConfig;
    }

    /**
     * @throws ClientException
     */
    public function resolveOption()
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

//        if ($this->shouldServerRetry($result)) {
//            return $this->request();
//        }

        if (!$result->isSuccess()) {
            throw new ServerException(
                $result,
                sprintf('%d %s', $result->getStatusCode(), $result->getReasonPhrase()),
                API::ERROR_SERVER_UNKNOWN
            );
        }

        return $result;
    }

    /**
     * @return Result
     * @throws ClientException
     */
    private function response(): Result
    {
        try {
            /* @var Result $result */
            $result = self::createClient($this)->request(
                $this->requestMethod(),
                (string)$this->uri,
                $this->options
            );
            return value($result);
        } catch (GuzzleException $exception) {
//            if ($this->shouldClientRetry($exception)) {
//                return $this->response();
//            }
            throw new ClientException(
                $exception->getMessage(),
                API::ERROR_CLIENT_UNKNOWN,
                $exception
            );
        }
    }

    /**
     * Remove redundant Query
     *
     * @codeCoverageIgnore
     */
    private function cleanOptions()
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
