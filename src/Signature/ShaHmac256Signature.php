<?php

namespace Pff\EasyApi\Signature;

use Pff\EasyApi\Contracts\SignatureInterface;

class ShaHmac256Signature implements SignatureInterface
{

    /**
     * @return string
     */
    public function getMethod()
    {
        return 'HMAC-SHA256';
    }

    /**
     * @return string
     */
    public function getType()
    {
        return '';
    }

    /**
     * @return string
     */
    public function getVersion()
    {
        return '1.0';
    }

    /**
     * @param string $string
     * @param string $secretString
     *
     * @return string
     */
    public function sign($string, $secretString)
    {
        return hash_hmac('sha256', $string, $secretString, false);
    }
}
