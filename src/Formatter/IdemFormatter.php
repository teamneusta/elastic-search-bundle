<?php declare(strict_types=1);

namespace Neusta\ElasticSearchBundle\Formatter;

class IdemFormatter implements Formatter
{
    public function format(mixed $value): mixed
    {
        return $value;
    }
}
