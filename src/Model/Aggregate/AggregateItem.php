<?php declare(strict_types=1);

namespace Neusta\ElasticSearchBundle\Model\Aggregate;

class AggregateItem
{
    public function __construct(
        private string $value,
        private int $count,
        private string $label,
    ) {
    }

    public function getValue(): string
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
