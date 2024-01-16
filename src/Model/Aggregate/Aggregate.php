<?php declare(strict_types=1);

namespace Neusta\ElasticSearchBundle\Model\Aggregate;

class Aggregate implements AggregateInterface
{
    /**
     * @param array<AggregateItem> $aggregateItems
     */
    public function __construct(
        private string $fieldName,
        private string $label,
        private array $aggregateItems = [],
    ) {
    }

    public function getFieldName(): string
    {
        return $this->fieldName;
    }

    public function getLabel(): string
    {
        return $this->label;
    }

    public function getAggregateItems(): array
    {
        return $this->aggregateItems;
    }

    public function addAggregateItem(AggregateItem $aggregateItem): void
    {
        $this->aggregateItems[] = $aggregateItem;
    }
}
