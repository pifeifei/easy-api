<?php

namespace Pff\EasyApi\Exception;

use Pff\EasyApi\Result;
use Throwable;

class ServerException extends ApiException
{
    protected $result;

    public function __construct(Result $result, $message = "", $code = 0, Throwable $previous = null)
    {
        parent::__construct($message, $code, $previous);
        $this->result = $result;
    }

    public function getResult()
    {
        return $this->result;
    }

    public function getRequest()
    {
        return $this->getResult()->getRequest();
    }

    public function getResponse()
    {
        return $this->getResult();
    }
}
