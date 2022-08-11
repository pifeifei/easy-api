<?php

declare(strict_types=1);

namespace Pff\EasyApi\Signature;

use Pff\EasyApi\Contracts\SignatureInterface;

class MD5Signature implements SignatureInterface
{
    public function getMethod(): string
    {
        return 'MD5';
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
        return md5($string . $secretString);
    }
}
