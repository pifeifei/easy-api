<?php

namespace Pff\EasyApi\Contracts;

interface SignatureInterface
{
    /**
     * @return string
     */
    public function getMethod();

    /**
     * @return string
     */
    public function getVersion();

    /**
     * @param string $string
     * @param string $secretString
     *
     * @return string
     */
    public function sign($string, $secretString);

    /**
     * @return string
     */
    public function getType();
}
