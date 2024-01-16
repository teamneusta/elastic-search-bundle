<?php declare(strict_types=1);

use ONGR\ElasticsearchDSL\Aggregation\AbstractAggregation;
use ONGR\ElasticsearchDSL\BuilderInterface;

interface Filter
{
    public function getFilter(array $options): ?BuilderInterface;

    public function getAggregation(): ?AbstractAggregation;
}
