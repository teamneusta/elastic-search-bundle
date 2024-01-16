<?php declare(strict_types=1);

namespace Neusta\ElasticSearchBundle\Factory;

use Neusta\ElasticSearchBundle\Model\Filter\BooleanFilter;
use Neusta\ElasticSearchBundle\Model\Filter\Filter;
use Neusta\ElasticSearchBundle\Model\Filter\FilterInterface;
use Neusta\ElasticSearchBundle\Model\Filter\MultiselectFilter;
use Neusta\ElasticSearchBundle\Model\Filter\RangeFilter;

class FilterFactory
{
    public const TYPE_MULTISELECT = 'multiselect';
    public const TYPE_RANGE = 'range';
    public const TYPE_BOOLEAN = 'boolean';

    public function create(string $fieldName, string $label, string $type): FilterInterface
    {
        return match ($type) {
            self::TYPE_MULTISELECT => new MultiselectFilter($fieldName, $label),
            self::TYPE_RANGE => new RangeFilter($fieldName, $label),
            self::TYPE_BOOLEAN => new BooleanFilter($fieldName, $label),
            default => new Filter($type, $fieldName, $label),
        };
    }
}
