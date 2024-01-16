<?php declare(strict_types=1);

namespace Neusta\ElasticSearchBundle\Factory\Elastica;

use Elastica\Aggregation\Terms;
use Neusta\ConverterBundle\Converter\Context\GenericContext;
use Neusta\ConverterBundle\TargetFactory;

/**
 * @implements TargetFactory<Terms, GenericContext>
 */
class TermsAggregationFactory implements TargetFactory
{
    public function create(object $ctx = null): object
    {
        return new Terms('');
    }
}
