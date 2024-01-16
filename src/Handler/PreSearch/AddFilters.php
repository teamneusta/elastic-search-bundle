<?php declare(strict_types=1);

namespace Neusta\ElasticSearchBundle\Handler\PreSearch;

use Neusta\ElasticSearchBundle\Builder\FilterBuilder;
use Neusta\ElasticSearchBundle\Handler\SearchHandler;
use Neusta\ElasticSearchBundle\SearchContext;

class AddFilters implements SearchHandler
{
    public function __construct(
        private FilterBuilder $filterBuilder,
    ) {
    }

    public function handle(SearchContext $searchContext): void
    {
        $searchQuery = $searchContext->getQuery();
        foreach ($searchContext->getFilters() as $filter) {
            if (!empty($filter->getFilterValues())) {
                $this->filterBuilder->addFilterToQuery($filter, $searchQuery);
            }
            $filter->cleanUpSelectedValues();
        }
    }
}
