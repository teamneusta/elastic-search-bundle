<?php declare(strict_types=1);

namespace Neusta\ElasticSearchBundle\Tests\Search\Handler\PreSearch;

use Elastica\Query;
use Neusta\ElasticSearchBundle\Factory\QueryFactory;
use Neusta\ElasticSearchBundle\Handler\PreSearch\StringSearchQuery;
use Neusta\ElasticSearchBundle\SearchContext;
use PHPUnit\Framework\TestCase;
use Prophecy\PhpUnit\ProphecyTrait;

class SearchQueryTest extends TestCase
{
    use ProphecyTrait;

    private StringSearchQuery $handler;

    protected function setUp(): void
    {
        $this->handler = new StringSearchQuery(new QueryFactory());
    }

    /** @test */
    public function handle_must_add_query_and_bool_query(): void
    {
        $searchContext = new SearchContext();
        $searchContext->setSearchTerm('such mich');

        $this->handler->handle($searchContext);

        // assert
        $query = $searchContext->getQuery();
        self::assertInstanceOf(Query::class, $query);
        $boolQuery = $query->getQuery();
        self::assertInstanceOf(Query\BoolQuery::class, $boolQuery);

        $stringQuery = $boolQuery->getParam('must')[0];
        self::assertInstanceOf(Query\QueryString::class, $stringQuery);
        self::assertSame('such mich', $stringQuery->getParam('query'));
    }
}
