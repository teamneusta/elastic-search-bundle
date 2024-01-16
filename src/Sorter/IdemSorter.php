<?php declare(strict_types=1);

namespace Neusta\ElasticSearchBundle\Sorter;

class IdemSorter implements Sorter
{
    public function sort(array $buckets): array
    {
        return $buckets;
    }
}
