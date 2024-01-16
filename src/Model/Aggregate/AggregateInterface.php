<?php declare(strict_types=1);

namespace Neusta\ElasticSearchBundle\Model\Aggregate;

interface AggregateInterface
{
    public function getLabel(): string;

    public function getFieldName(): string;

    /** @return array<AggregateItem> */
    public function getAggregateItems(): array;

    public function addAggregateItem(AggregateItem $aggregateItem): void;
}
