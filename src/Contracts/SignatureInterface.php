<?php

declare(strict_types=1);

namespace Pff\EasyApi\Contracts;

interface SignatureInterface
{
    public function getMethod(): string;

    public function getVersion(): string;

    public function sign(string $string, string $secretString): string;

    public function getType(): string;
}
