<?php declare(strict_types=1);

namespace Neusta\ElasticSearchBundle\Handler\PostSearch;

use JoliCode\Elastically\Model\Document;
use JoliCode\Elastically\Result;
use Neusta\ElasticSearchBundle\Handler\SearchHandler;
use Neusta\ElasticSearchBundle\SearchContext;

class SearchResults implements SearchHandler
{
    public function handle(SearchContext $searchContext): void
    {
        $resultSet = $searchContext->getResultSet();

        $elasticaResults = $resultSet->getResults();

        $searchResults = array_map(
            // Elastically uses a custom ResultSetBuilder, which transforms the Elastica Results into Elastically
            // Results. The builder is integrated in \JoliCode\Elastically\Index.
            // Even if the type says, that the result set return \Elastica\Result, it is
            // actually a \JoliCode\Elastically\Result.
            // TODO remove BundleDocument and use the Elastically Document
            /** @phpstan-ignore-next-line */
            static function (Result $elasticaResult) {
                /** @var Document $model */
                $model = $elasticaResult->getModel();
                /* @phpstan-ignore-next-line */
                $model->_id = (string) $elasticaResult->getId();

                return $model;
            },
            $elasticaResults,
        );

        $searchContext->setSearchResults($searchResults);
        $searchContext->setTotalCount($resultSet->getTotalHits());
    }
}
