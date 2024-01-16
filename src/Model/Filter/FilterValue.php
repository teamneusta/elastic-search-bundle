<?php declare(strict_types=1);

namespace Neusta\ElasticSearchBundle\Model\Filter;

class FilterValue
{
    public function __construct(
        private string|int|float $value,
        private int $count,
        private string $label,
    ) {
    }

    public function getValue(): string|int|float
    {
        return $this->value;
    }

    public function getCount(): int
    {
        return $this->count;
    }

    public function getLabel(): string
    {
        return $this->label;
    }
}
