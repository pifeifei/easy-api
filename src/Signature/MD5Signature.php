<?php

namespace Pff\EasyApi\Signature;

use Pff\EasyApi\Contracts\SignatureInterface;

class MD5Signature implements SignatureInterface
{

    /**
     * @return string
     */
    public function getMethod()
    {
        return 'MD5';
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
    public function sign($string, $secretString = null)
    {
        return md5($string . $secretString);
    }
}
