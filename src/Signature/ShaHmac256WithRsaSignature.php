<?php

declare(strict_types=1);

namespace Pff\EasyApi\Signature;

use Pff\EasyApi\Contracts\SignatureInterface;

class ShaHmac256WithRsaSignature implements SignatureInterface
{
    public function getMethod(): string
    {
        return 'SHA256withRSA';
    }

    public function getType(): string
    {
        return 'PRIVATEKEY';
    }

    public function getVersion(): string
    {
        return '1.0';
    }

    public function sign(string $string, string $secretString): string
    {
        $binarySignature = '';
        openssl_sign(
            $string,
            $binarySignature,
            $secretString,
            OPENSSL_ALGO_SHA256
        );

        return base64_encode($binarySignature);
    }
}
