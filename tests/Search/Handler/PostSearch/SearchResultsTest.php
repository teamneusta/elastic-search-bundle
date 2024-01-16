<?php declare(strict_types=1);

namespace Neusta\ElasticSearchBundle\Tests\Search\Handler\PostSearch;

use Elastica\Query;
use Elastica\Response;
use Elastica\ResultSet;
use JoliCode\Elastically\Model\Document;
use Neusta\ElasticSearchBundle\Handler\PostSearch\SearchResults;
use Neusta\ElasticSearchBundle\SearchContext;
use PHPUnit\Framework\TestCase;

class SearchResultsTest extends TestCase
{
    /** @test */
    public function put_empty_search_result_into_context(): void
    {
        $searchResults = new SearchResults();
        $searchContext = new SearchContext();
        $emptyResultSet = new ResultSet(new Response(''), new Query(), []);
        $searchContext->setResultSet($emptyResultSet);

        $searchResults->handle($searchContext);

        self::assertEmpty($searchContext->getSearchResults());
    }

    /** @test */
    public function add_id_to_elastically_results(): void
    {
        $searchResults = new SearchResults();
        $searchContext = new SearchContext();

        $document = new Document('42');

        $result = new \JoliCode\Elastically\Result([]);
        $result->setParam('_id', 42);
        $result->setModel($document);

        $resultSet = new ResultSet(new Response(''), new Query(), [$result]);
        $searchContext->setResultSet($resultSet);

        // act
        $searchResults->handle($searchContext);

        self::assertCount(1, $searchContext->getSearchResults());
        self::assertSame('42', $searchContext->getSearchResults()[0]->_id);
    }
}
