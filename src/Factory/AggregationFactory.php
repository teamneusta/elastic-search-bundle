<?php declare(strict_types=1);

namespace Neusta\ElasticSearchBundle\Factory;

use Neusta\ElasticSearchBundle\Exception\UnknownTypeException;
use Neusta\ElasticSearchBundle\Model\Aggregate\AggregateInterface;
use Neusta\ElasticSearchBundle\Model\Aggregate\BooleanAggregate;
use Neusta\ElasticSearchBundle\Model\Aggregate\MultiselectAggregate;
use Neusta\ElasticSearchBundle\Model\Aggregate\RangeAggregate;

class AggregationFactory
{
    public const TYPE_MULTISELECT = 'multiselect';
    public const TYPE_RANGE = 'range';
    public const TYPE_BOOLEAN = 'boolean';

    public function create(string $fieldName, string $label, string $type): AggregateInterface
    {
        return match ($type) {
            self::TYPE_MULTISELECT => new MultiselectAggregate($fieldName, $label),
            self::TYPE_RANGE => new RangeAggregate($fieldName, $label),
            self::TYPE_BOOLEAN => new BooleanAggregate($fieldName, $label),
            default => throw new UnknownTypeException($type),
        };
    }
}
