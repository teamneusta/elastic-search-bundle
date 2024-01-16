<?php declare(strict_types=1);

namespace Neusta\ElasticSearchBundle\Tests\Search\Factory;

use Elastica\Query;
use Neusta\ElasticSearchBundle\Factory\QueryFactory;
use Neusta\ElasticSearchBundle\SearchContext;
use PHPUnit\Framework\TestCase;
use Prophecy\PhpUnit\ProphecyTrait;

use function PHPUnit\Framework\assertEquals;

class QueryFactoryTest extends TestCase
{
    use ProphecyTrait;

    private QueryFactory $queryFactory;

    protected function setUp(): void
    {
        $this->queryFactory = new QueryFactory();
    }

    /** @test */
    public function testCreateSearchQuery(): void
    {
        $searchContext = new SearchContext();
        $searchContext->setPage(3);
        $searchContext->setItemsPerPage(25);
        $searchContext->setActivePagination(true);
        $searchQuery = $this->queryFactory->createSearchQuery($searchContext);

        self::assertInstanceOf(Query::class, $searchQuery);
        self::assertInstanceOf(Query\BoolQuery::class, $searchQuery->getQuery());
        self::assertEquals(25, $searchQuery->getParam('size'));
        self::assertEquals(50, $searchQuery->getParam('from'));
    }

    /** @test */
    public function testCreateRangeQuery(): void
    {
        $expectedFieldParam = [
            'gte' => 10,
            'lte' => 40,
        ];

        $rangeQuery = $this->queryFactory->createClosedRangeQuery('my_price_field', 10, 40);

        self::assertSame($expectedFieldParam, $rangeQuery->getParam('my_price_field'));
    }

    /** @test */
    public function testAddAggregation(): void
    {
        $expectedAggregationList = [
            'my_aggregate' => [
                'terms' => ['field' => 'my_field_name'],
            ],
        ];

        $innerQuery = new Query\BoolQuery();
        $searchQuery = new Query($innerQuery);

        $this->queryFactory->addAggregation($searchQuery, 'my_aggregate', 'my_field_name');

        self::assertSame($expectedAggregationList, $searchQuery->toArray()['aggs']);
    }

    /** @test */
    public function testAddMustQuery(): void
    {
        $innerQuery = new Query\BoolQuery();
        $searchQuery = new Query($innerQuery);

        $queryMock = $this->prophesize(Query\AbstractQuery::class);
        $queryMock->toArray()->willReturn(['0' => 'test']);

        $this->queryFactory->addMustQuery($searchQuery, $queryMock->reveal());
        assertEquals(
            [
                'bool' => [
                    'must' => [
                        0 => [
                            '0' => 'test',
                        ],
                    ],
                ],
            ],
            $innerQuery->toArray(),
        );
    }

    /** @test */
    public function testAddMustNotQuery(): void
    {
        $innerQuery = new Query\BoolQuery();
        $searchQuery = new Query($innerQuery);

        $queryMock = $this->prophesize(Query\AbstractQuery::class);
        $queryMock->toArray()->willReturn(['0' => 'test']);

        $this->queryFactory->addMustNotQuery($searchQuery, $queryMock->reveal());
        assertEquals(
            [
                'bool' => [
                    'must_not' => [
                        0 => [
                            '0' => 'test',
                        ],
                    ],
                ],
            ],
            $innerQuery->toArray(),
        );
    }
}
