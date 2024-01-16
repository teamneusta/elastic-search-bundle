<?php declare(strict_types=1);

namespace Neusta\ElasticSearchBundle\Formatter;

interface Formatter
{
    public function format(mixed $value): mixed;
}
