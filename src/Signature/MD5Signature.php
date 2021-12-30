<?php

namespace Pff\EasyApi\Signature;

use Pff\EasyApi\Contracts\SignatureInterface;

class MD5Signature implements SignatureInterface
{

    /**
     * @return string
     */
    public function getMethod(): string
    {
        return 'MD5';
    }

    /**
     * @return string
     */
    public function getType(): string
    {
        return '';
    }

    /**
     * @return string
     */
    public function getVersion(): string
    {
        return '1.0';
    }

    /**
     * @param string $string
     * @param string $secretString
     *
     * @return string
     */
    public function sign(string $string, string $secretString): string
    {
        return md5($string . $secretString);
    }
}
