<?php declare(strict_types=1);

namespace Neusta\ElasticSearchBundle\Tests\Search\Handler\Search;

use Elastica\Exception\InvalidException;
use Elastica\ResultSet;
use Neusta\ElasticSearchBundle\Formatter\Formatter;
use Neusta\ElasticSearchBundle\Handler\Search\CollectMultiselectAggregationValues;
use Neusta\ElasticSearchBundle\Model\Aggregate\Aggregate;
use Neusta\ElasticSearchBundle\Model\Aggregate\MultiselectAggregate;
use Neusta\ElasticSearchBundle\Model\Aggregate\RangeAggregate;
use Neusta\ElasticSearchBundle\SearchContext;
use PHPUnit\Framework\TestCase;
use Prophecy\PhpUnit\ProphecyTrait;

class CollectMultiselectAggregationValuesTest extends TestCase
{
    use ProphecyTrait;

    private CollectMultiselectAggregationValues $handler;

    protected function setUp(): void
    {
        // create dependency mocks
        $formatter = $this->prophesize(Formatter::class);

        // create test object
        $this->handler = new CollectMultiselectAggregationValues(
            'my-aggregation',
            $formatter->reveal(),
        );

        // mock dependency behaviour
        $formatter->format('pot1')->willReturn('POT 1');
        $formatter->format('pot2')->willReturn('POT 2');
        $formatter->format('pot3')->willReturn('POT 3');
    }

    /** @test */
    public function handle_must_add_aggregation_item_to_aggregate(): void
    {
        $searchContext = new SearchContext();
        $aggregate = new MultiselectAggregate('test-field', 'test-label');
        $searchContext->addAggregation($aggregate, 'my-aggregation');

        $resultSet = $this->prophesize(ResultSet::class);
        $resultSet->getAggregation('my-aggregation')->willReturn([
            'buckets' => [
                ['key' => 'pot1', 'doc_count' => 1],
                ['key' => 'pot2', 'doc_count' => 2],
                ['key' => 'pot3', 'doc_count' => 3],
            ],
        ]);
        $searchContext->setResultSet($resultSet->reveal());

        // act
        $this->handler->handle($searchContext);

        self::assertSame('POT 1', $aggregate->getAggregateItems()[0]->getLabel());
        self::assertSame('pot1', $aggregate->getAggregateItems()[0]->getValue());
        self::assertSame(1, $aggregate->getAggregateItems()[0]->getCount());

        self::assertSame('POT 2', $aggregate->getAggregateItems()[1]->getLabel());
        self::assertSame('pot2', $aggregate->getAggregateItems()[1]->getValue());
        self::assertSame(2, $aggregate->getAggregateItems()[1]->getCount());

        self::assertSame('POT 3', $aggregate->getAggregateItems()[2]->getLabel());
        self::assertSame('pot3', $aggregate->getAggregateItems()[2]->getValue());
        self::assertSame(3, $aggregate->getAggregateItems()[2]->getCount());
    }

    /** @test */
    public function do_not_add_aggregation_items_when_aggregate_from_context_has_wrong_type(): void
    {
        $searchContext = new SearchContext();
        $resultSet = $this->prophesize(ResultSet::class);
        $searchContext->setResultSet($resultSet->reveal());

        $aggregateFromWrongType = new RangeAggregate('test-field', 'test-label');
        $searchContext->addAggregation($aggregateFromWrongType, 'my-aggregation');

        // act
        $this->handler->handle($searchContext);

        self::assertEmpty($aggregateFromWrongType->getAggregateItems());
    }

    /** @test */
    public function do_not_add_aggregation_when_result_set_throws_exception(): void
    {
        $searchContext = new SearchContext();
        $aggregate = new Aggregate('test-field', 'test-label');
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
