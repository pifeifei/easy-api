<?php

declare(strict_types=1);

namespace Pff\EasyApi\Format;

use Pff\EasyApi\Utils;

class HttpBinFormatter extends AbstractFormatter
{
    /**
     * {@inheritDoc}
     */
    public function resolve(): void
    {
        $this->query();
        $this->body();
    }

    /**
     * {@inheritDoc}
     */
    protected function getData(): array
    {
        return Utils::ksortRecursive(Utils::boolToString($this->client->data()->all()));
    }

    /**
     * {@inheritDoc}
     */
    protected function getQuery()
    {
        return Utils::valueToJsonString(Utils::ksortRecursive(Utils::boolToString($this->client->query()->all())));
    }
}
