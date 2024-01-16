<?php declare(strict_types=1);

namespace Neusta\ElasticSearchBundle\Handler\PreSearch;

use Elastica\Aggregation\Terms;
use Neusta\ConverterBundle\Converter;
use Neusta\ConverterBundle\Converter\Context\GenericContext;
use Neusta\ElasticSearchBundle\Handler\SearchHandler;
use Neusta\ElasticSearchBundle\Model\Aggregate\AggregateInterface;
use Neusta\ElasticSearchBundle\SearchContext;

class AddAggregations implements SearchHandler
{
    /**
     * @param Converter<AggregateInterface, Terms, GenericContext> $aggregationConverter
     */
    public function __construct(
        private Converter $aggregationConverter,
    ) {
    }

    public function handle(SearchContext $searchContext): void
    {
        $searchQuery = $searchContext->getQuery();
        foreach ($searchContext->getAggregations() as $aggregation) {
            $searchQuery->addAggregation($this->aggregationConverter->convert($aggregation));
        }
    }
}
