<?php declare(strict_types=1);

namespace Neusta\ElasticSearchBundle\Tests\Search\Handler\Search;

use Elastica\Query;
use Elastica\ResultSet;
use JoliCode\Elastically\Index;
use Neusta\ElasticSearchBundle\Handler\Search\SearchExecution;
use Neusta\ElasticSearchBundle\SearchContext;
use PHPUnit\Framework\TestCase;
use Prophecy\PhpUnit\ProphecyTrait;

class SearchExecutionTest extends TestCase
{
    use ProphecyTrait;

    private SearchExecution $handler;

    protected function setUp(): void
    {
        $this->handler = new SearchExecution();
    }

    /** @test */
    public function handle_must_execute_search_and_write_result_set_to_context(): void
    {
        $searchContext = new SearchContext();

        $index = $this->prophesize(Index::class);
        $searchContext->setIndex($index->reveal());

        $query = $this->prophesize(Query::class);
        $searchContext->setQuery($query->reveal());

        $resultSet = $this->prophesize(ResultSet::class);
        $index->search($query->reveal())->willReturn($resultSet->reveal());

        // act
        $this->handler->handle($searchContext);

        self::assertSame($resultSet->reveal(), $searchContext->getResultSet());
    }
}
