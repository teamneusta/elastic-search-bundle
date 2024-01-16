<?php declare(strict_types=1);

namespace Neusta\ElasticSearchBundle\Tests\Search\Handler\Search;

use Elastica\Exception\InvalidException;
use Elastica\ResultSet;
use Neusta\ElasticSearchBundle\Formatter\Formatter;
use Neusta\ElasticSearchBundle\Handler\Search\CollectRangeAggregationValues;
use Neusta\ElasticSearchBundle\Model\Aggregate\MultiselectAggregate;
use Neusta\ElasticSearchBundle\Model\Aggregate\RangeAggregate;
use Neusta\ElasticSearchBundle\SearchContext;
use PHPUnit\Framework\TestCase;
use Prophecy\PhpUnit\ProphecyTrait;

class CollectRangeAggregationValuesTest extends TestCase
{
    use ProphecyTrait;

    private CollectRangeAggregationValues $handler;

    protected function setUp(): void
    {
        // create dependency mocks
        $formatter = $this->prophesize(Formatter::class);

        // create test object
        $this->handler = new CollectRangeAggregationValues(
            'my-aggregation',
            $formatter->reveal(),
        );

        // mock dependency behaviour
        $formatter->format('40')->willReturn(40);
        $formatter->format('10')->willReturn(10);
        $formatter->format('90')->willReturn(90);
        $formatter->format('70')->willReturn(70);
    }

    /** @test */
    public function handle_must_add_aggregation_item_to_aggregate(): void
    {
        $searchContext = new SearchContext();

        $aggregate = new RangeAggregate('test-field', 'test-label');
        $aggregate->setCurrentValueMin(10);
        $aggregate->setCurrentValueMax(90);

        $searchContext->addAggregation($aggregate, 'my-aggregation');

        $resultSet = $this->prophesize(ResultSet::class);
        $resultSet->getAggregation('my-aggregation')->willReturn([
            'buckets' => [
                ['key' => '40', 'doc_count' => 3],
                ['key' => '10', 'doc_count' => 1],
                ['key' => '90', 'doc_count' => 4],
                ['key' => '70', 'doc_count' => 2],
            ],
        ]);
        $searchContext->setResultSet($resultSet->reveal());

        // act
        $this->handler->handle($searchContext);

        self::assertSame('40', $aggregate->getAggregateItems()[0]->getValue());
        self::assertSame(3, $aggregate->getAggregateItems()[0]->getCount());

        self::assertSame('10', $aggregate->getAggregateItems()[1]->getValue());
        self::assertSame(1, $aggregate->getAggregateItems()[1]->getCount());

        self::assertSame('90', $aggregate->getAggregateItems()[2]->getValue());
        self::assertSame(4, $aggregate->getAggregateItems()[2]->getCount());

        self::assertSame('70', $aggregate->getAggregateItems()[3]->getValue());
        self::assertSame(2, $aggregate->getAggregateItems()[3]->getCount());
    }

    /** @test */
    public function do_not_add_aggregation_items_when_aggregate_from_context_has_wrong_type(): void
    {
        $searchContext = new SearchContext();
        $resultSet = $this->prophesize(ResultSet::class);
        $searchContext->setResultSet($resultSet->reveal());

        $aggregateFromWrongType = new MultiselectAggregate('test-field', 'test-label');
        $searchContext->addAggregation($aggregateFromWrongType, 'my-aggregation');

        // act
        $this->handler->handle($searchContext);

        self::assertEmpty($aggregateFromWrongType->getAggregateItems());
    }

    /** @test */
    public function do_not_add_aggregation_when_result_set_throws_exception(): void
    {
        $searchContext = new SearchContext();
        $aggregate = new RangeAggregate('test-field', 'test-label');
        $searchContext->addAggregation($aggregate, 'my-aggregation');

        $resultSet = $this->prophesize(ResultSet::class);
        $resultSet->getAggregation('my-aggregation')
            ->willThrow(InvalidException::class);
        $searchContext->setResultSet($resultSet->reveal());

        // act
        $this->handler->handle($searchContext);

        self::assertEmpty($aggregate->getAggregateItems());
    }
}
