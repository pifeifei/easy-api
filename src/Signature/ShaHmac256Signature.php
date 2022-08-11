<?php

declare(strict_types=1);

namespace Pff\EasyApi\Signature;

use Pff\EasyApi\Contracts\SignatureInterface;

class ShaHmac256Signature implements SignatureInterface
{
    public function getMethod(): string
    {
        return 'HMAC-SHA256';
    }

    public function getType(): string
    {
        return '';
    }

    public function getVersion(): string
    {
        return '1.0';
    }

    public function sign(string $string, string $secretString): string
    {
        return hash_hmac('sha256', $string, $secretString, false);
    }
}
