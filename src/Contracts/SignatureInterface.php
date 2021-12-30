<?php

namespace Pff\EasyApi\Contracts;

interface SignatureInterface
{
    /**
     * @return string
     */
    public function getMethod(): string;

    /**
     * @return string
     */
    public function getVersion(): string;

    /**
     * @param string $string
     * @param string $secretString
     *
     * @return string
     */
    public function sign(string $string, string $secretString): string;

    /**
     * @return string
     */
    public function getType(): string;
}
