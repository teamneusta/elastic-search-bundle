<?php declare(strict_types=1);

namespace Neusta\ElasticSearchBundle\Handler\PreSearch;

use Neusta\ElasticSearchBundle\Exception\InvalidQueryException;
use Neusta\ElasticSearchBundle\Factory\QueryFactory;
use Neusta\ElasticSearchBundle\Handler\SearchHandler;
use Neusta\ElasticSearchBundle\SearchContext;

class StringSearchQuery implements SearchHandler
{
    public function __construct(
        private QueryFactory $queryFactory,
    ) {
    }

    public function handle(SearchContext $searchContext): void
    {
        $searchQuery = $this->queryFactory->createSearchQuery($searchContext);

        try {
            $this->queryFactory->addMustQuery(
                $searchQuery,
                $this->queryFactory->createQueryStringQuery($searchContext->getSearchTerm()),
            );
        } catch (InvalidQueryException $e) {
            // TODO handle the exception in higher level
        }

        $searchContext->setQuery($searchQuery);
    }
}
