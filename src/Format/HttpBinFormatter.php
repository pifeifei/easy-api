<?php

declare(strict_types=1);

namespace Pff\EasyApi\Format;

use Pff\EasyApi\Utils;

class HttpBinFormatter extends AbstractFormatter
{
    public function resolve(): void
    {
        $this->query();
        $this->body();
    }

    protected function getData(): array
    {
        return Utils::ksortRecursive(Utils::boolToString($this->client->getData()->all()));
    }

    protected function getQuery()
    {
        return Utils::valueToJsonString(Utils::ksortRecursive(Utils::boolToString($this->client->getQuery()->all())));
    }
}
