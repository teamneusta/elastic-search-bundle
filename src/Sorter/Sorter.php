<?php declare(strict_types=1);

namespace Neusta\ElasticSearchBundle\Sorter;

interface Sorter
{
    /**
     * @param array<string, mixed> $buckets
     *
     * @return array<string, mixed>
     */
    public function sort(array $buckets): array;
}
