<?php

declare(strict_types=1);

namespace Pff\EasyApi;

use ArrayAccess;
use Countable;
use GuzzleHttp\Psr7\Response;
use GuzzleHttp\Utils;
use InvalidArgumentException;
use IteratorAggregate;
use Pff\EasyApi\Concerns\DataTrait;
use Pff\EasyApi\Exception\ClientException;
use Pff\EasyApi\Request\Request;
use Psr\Http\Message\ResponseInterface;

use function strtoupper;

class Result extends Response implements ArrayAccess, IteratorAggregate, Countable
{
    use DataTrait;

    /**
     * Instance of the request.
     *
     * @var null|Request
     */
    protected $request;

    /**
     * Result constructor.
     */
    public function __construct(ResponseInterface $response, Request $request = null)
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

    /**
     * @return Request
     */
    public function getRequest(): ?Request
    {
        return $this->request;
    }

    public function isSuccess(): bool
    {
        return 200 <= $this->getStatusCode()
            && 300 > $this->getStatusCode();
    }

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
        return ($this->request instanceof Request)
            ? strtoupper($this->request->format())
            : API::RESPONSE_FORMAT_JSON;
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

        /** @var array<string, mixed> $arr */
        $arr = json_decode(json_encode($json), true);

        if (JSON_ERROR_NONE !== json_last_error()) {
            throw new ClientException('API xml parse error: ' . json_last_error_msg());
        }

        return $arr;
    }
}
