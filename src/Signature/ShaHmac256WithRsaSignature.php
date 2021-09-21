<?php

namespace Pff\EasyApi\Signature;

use Pff\EasyApi\Contracts\SignatureInterface;

class ShaHmac256WithRsaSignature implements SignatureInterface
{
    /**
     * @return string
     */
    public function getMethod()
    {
        return 'SHA256withRSA';
    }

    /**
     * @return string
     */
    public function getType()
    {
        return 'PRIVATEKEY';
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
        $binarySignature = '';
        openssl_sign(
            $string,
            $binarySignature,
            $secretString,
            \OPENSSL_ALGO_SHA256
        );

        return base64_encode($binarySignature);
    }
}
