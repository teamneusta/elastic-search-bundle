<?php declare(strict_types=1);

namespace Neusta\ElasticSearchBundle\Populator\Elastica;

use Elastica\Aggregation\Terms;
use Neusta\ConverterBundle\Converter\Context\GenericContext;
use Neusta\ConverterBundle\Populator;
use Neusta\ElasticSearchBundle\Model\Aggregate\AggregateInterface;
use Neusta\ElasticSearchBundle\Model\Aggregate\MultiselectAggregate;

/**
 * @implements Populator<AggregateInterface, Terms, GenericContext>
 */
class AggregationOrderPopulator implements Populator
{
    public function populate(object $target, object $source, object $ctx = null): void
    {
        if ($source instanceof MultiselectAggregate) {
            $target->setOrder('_term', 'asc');
        }
    }
}
