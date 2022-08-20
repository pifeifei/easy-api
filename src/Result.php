<?php

declare(strict_types=1);

namespace Pff\EasyApi;

use ArrayAccess;
use Countable;
use GuzzleHttp\Psr7\Response;
use InvalidArgumentException;
use IteratorAggregate;
use Pff\EasyApi\Concerns\DataTrait;
use Pff\EasyApi\Exception\ClientException;
use Pff\EasyApi\Request\Request;
use Psr\Http\Message\ResponseInterface;

use function strtoupper;

/**
 * @template TKey as array-key
 * @template TValue as mixed
 *
 * @implements IteratorAggregate<TKey, TValue>
 * @implements ArrayAccess<TKey, TValue>
 */
class Result extends Response implements ArrayAccess, IteratorAggregate, Countable
{
    use DataTrait;

    /**
     * Instance of the request.
     */
    protected Request $request;

    /**
     * Result constructor.
     *
     * @throws ClientException
     */
    public function __construct(ResponseInterface $response, Request $request)
    {
        parent::__construct(
            $response->getStatusCode(),
            $response->getHeaders(),
            $response->getBody(),
            $response->getProtocolVersion(),
            $response->getReasonPhrase()
        );

        $this->request = $request;

        $this->resolveData();
    }

    /**
     * @return string
     */
    public function __toString()
    {
        return (string) $this->getBody();
    }

    public function getRequest(): Request
    {
        return $this->request;
    }

    public function isSuccess(): bool
    {
        return 200 <= $this->getStatusCode()
            && 300 > $this->getStatusCode();
    }

    /**
     * @throws ClientException
     */
    private function resolveData(): void
    {
        $content = $this->getBody()->__toString();

        switch ($this->getRequestFormat()) {
            case API::RESPONSE_FORMAT_RAW:
            case API::RESPONSE_FORMAT_JSON:
                $result_data = $this->jsonToArray($content);

                break;

            case API::RESPONSE_FORMAT_XML:
                $result_data = $this->xmlToArray($content);

                break;

            default:
                $result_data = $this->jsonToArray($content);
        }

        if (!$result_data) {
            $result_data = [];
        }

        $this->collection($result_data);
    }

    private function getRequestFormat(): string
    {
        if ($this->request instanceof Request) {
            if ($format = $this->request->format()) {
                return strtoupper($format);
            }
        }

        return API::RESPONSE_FORMAT_JSON;
    }

    /**
     * @return array<string, mixed>
     */
    private function jsonToArray(string $jsonString): array
    {
        try {
            return Utils::jsonDecode($jsonString, true);
        } catch (InvalidArgumentException $exception) {
            return [];
        }
    }

    /**
     * @throws ClientException
     *
     * @return array<string, mixed>
     */
    private function xmlToArray(string $string): array
    {
        /** @var array<string, mixed>|false $json */
        $json = simplexml_load_string($string);
        if (false === $json) {
            throw new ClientException('API xml parse error: ' . $string);
        }

        /** @var array<string, mixed> */
        return (array) $json;
    }
}
