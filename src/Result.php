<?php

namespace Pff\EasyApi;

use ArrayAccess;
use Countable;
use Exception;
use GuzzleHttp\Psr7\Response;
use GuzzleHttp\Utils;
use InvalidArgumentException;
use IteratorAggregate;
use Pff\EasyApi\Concerns\DataTrait;
use Pff\EasyApi\Request\Request;
use Psr\Http\Message\ResponseInterface;

class Result extends Response implements ArrayAccess, IteratorAggregate, Countable
{
    use DataTrait;

    /**
     * Instance of the request.
     *
     * @var Request
     */
    protected $request;

    /**
     * Result constructor.
     *
     * @param ResponseInterface $response
     * @param Request           $request
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
        // echo __METHOD__;
//dump($response->getBody()->__toString());
        $this->request = $request;

        $this->resolveData();
    }

    private function resolveData()
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

    /**
     * @return string
     */
    private function getRequestFormat()
    {
        return ($this->request instanceof Request)
            ? \strtoupper($this->request->format())
            : API::RESPONSE_FORMAT_JSON;
    }

    /**
     * @param string $jsonString
     *
     * @return array
     */
    private function jsonToArray($jsonString)
    {
        try {
            return Utils::jsonDecode($jsonString, true);
        } catch (InvalidArgumentException $exception) {
            return [];
        }
    }

    /**
     * @param string $string
     *
     * @return array
     */
    private function xmlToArray($string)
    {
        try {
            return json_decode(json_encode(simplexml_load_string($string)), true);
        } catch (Exception $exception) {
            return [];
        }
    }

    /**
     * @return string
     */
    public function __toString()
    {
        return (string)$this->getBody();
    }

    /**
     * @return Request
     */
    public function getRequest()
    {
        return $this->request;
    }

    /**
     * @return bool
     */
    public function isSuccess()
    {
        return 200 <= $this->getStatusCode()
            && 300 > $this->getStatusCode();
    }
}
