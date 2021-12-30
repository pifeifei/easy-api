<?php

namespace Pff\EasyApi\Signature;

use Pff\EasyApi\Contracts\SignatureInterface;

class ShaHmac256Signature implements SignatureInterface
{

    /**
     * @return string
     */
    public function getMethod(): string
    {
        return 'HMAC-SHA256';
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
        return hash_hmac('sha256', $string, $secretString, false);
    }
}
