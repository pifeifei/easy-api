<?php

declare(strict_types=1);

namespace Pff\EasyApi;

class API
{
    public const VERSION = '0.1-dev';

    public const METHOD_GET = 'GET';

    public const METHOD_JSON = 'JSON';

    public const METHOD_POST = 'POST';

    public const METHOD_XML = 'XML';

    public const RESPONSE_FORMAT_RAW = 'RAW';

    public const RESPONSE_FORMAT_JSON = 'JSON';

    public const RESPONSE_FORMAT_XML = 'XML';

    public const RESPONSE_FORMAT_BIN = 'BIN';

    public const SIGN_POSITION_GET = 'GET';

    public const SIGN_POSITION_NONE = 'NONE'; // 不用签名

    public const SIGN_POSITION_HEAD = 'HEAD';

    public const SIGN_POSITION_POST = 'POST';

    public const ERROR_CLIENT_UNKNOWN = 40000;

    public const ERROR_CLIENT_UNSUPPORTED_METHOD = 40001;

    public const ERROR_CLIENT_UNSUPPORTED_SIGNATURE = 40002;

    public const ERROR_CLIENT_FAILED_GET_ACCESS = 40003;

    public const ERROR_SERVER_UNKNOWN = 50000;

    public const ERROR_SERVER_INVALID_APP_KEY = 50001;

    public const ERROR_SERVER_INVALID_APP_SECRET = 50002;
}
