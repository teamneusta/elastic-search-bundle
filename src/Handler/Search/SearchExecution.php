<?php declare(strict_types=1);

namespace Neusta\ElasticSearchBundle\Handler\Search;

use Neusta\ElasticSearchBundle\Handler\SearchHandler;
use Neusta\ElasticSearchBundle\SearchContext;

class SearchExecution implements SearchHandler
{
    public function handle(SearchContext $searchContext): void
    {
        $query = $searchContext->getQuery();
        $searchContext->setResultSet($searchContext->getIndex()->search($query));
    }
}
