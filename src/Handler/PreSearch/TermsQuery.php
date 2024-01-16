<?php declare(strict_types=1);

namespace Neusta\ElasticSearchBundle\Handler\PreSearch;

use Neusta\ElasticSearchBundle\Factory\QueryFactory;
use Neusta\ElasticSearchBundle\Handler\SearchHandler;
use Neusta\ElasticSearchBundle\SearchContext;

class TermsQuery implements SearchHandler
{
    public function __construct(
        private QueryFactory $queryFactory,
        private string $fieldName,
        private string $keyToCompareValues,
    ) {
    }

    public function handle(SearchContext $searchContext): void
    {
        $searchQuery = $this->queryFactory->createSearchQuery($searchContext);

        $this->queryFactory->addMustQuery(
            $searchQuery,
            $this->queryFactory->createTermsQuery(
                $this->fieldName,
                $searchContext->getValue($this->keyToCompareValues),
            ),
        );

        $searchContext->setQuery($searchQuery);
    }
}
