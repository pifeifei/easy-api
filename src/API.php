<?php

namespace Pff\EasyApi;

class API
{
    const VERSION = '0.1-dev';

    const METHOD_GET = 'GET';

    const METHOD_JSON = 'JSON';

    const METHOD_POST = 'POST';

    const METHOD_XML = 'XML';

    const RESPONSE_FORMAT_RAW = 'RAW';

    const RESPONSE_FORMAT_JSON = 'JSON';

    const RESPONSE_FORMAT_XML = 'XML';

    const RESPONSE_FORMAT_BIN = 'BIN';

    const SIGN_POSITION_GET = 'GET';

    const SIGN_POSITION_NONE = 'NONE'; // 不用签名

    const SIGN_POSITION_HEAD = 'HEAD';

    const SIGN_POSITION_POST = 'POST';

    const ERROR_CLIENT_UNKNOWN  = 40000;

    const ERROR_CLIENT_UNSUPPORTED_METHOD = 40001;

    const ERROR_CLIENT_UNSUPPORTED_SIGNATURE = 40002;

    const ERROR_CLIENT_FAILED_GET_ACCESS = 40003;

    const ERROR_SERVER_UNKNOWN = 50000;

    const ERROR_SERVER_INVALID_APP_KEY = 50001;

    const ERROR_SERVER_INVALID_APP_SECRET = 50002;
}
