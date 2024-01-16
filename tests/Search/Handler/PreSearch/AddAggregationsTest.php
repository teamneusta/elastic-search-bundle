<?php declare(strict_types=1);

namespace Neusta\ElasticSearchBundle\Tests\Search\Handler\PreSearch;

use Elastica\Aggregation\Terms;
use Elastica\Query;
use Neusta\ConverterBundle\Converter;
use Neusta\ElasticSearchBundle\Handler\PreSearch\AddAggregations;
use Neusta\ElasticSearchBundle\Model\Aggregate\Aggregate;
use Neusta\ElasticSearchBundle\SearchContext;
use PHPUnit\Framework\TestCase;
use Prophecy\Argument;
use Prophecy\PhpUnit\ProphecyTrait;
use Prophecy\Prophecy\ObjectProphecy;

class AddAggregationsTest extends TestCase
{
    use ProphecyTrait;

    private AddAggregations $handler;

    private ObjectProphecy|Converter $converter;

    private ObjectProphecy|Query $mainQuery;
    private SearchContext $searchContext;

    protected function setUp(): void
    {
        // mock dependencies
        $this->converter = $this->prophesize(Converter::class);

        // mock test object
        $this->handler = new AddAggregations(
            $this->converter->reveal(),
        );

        // mock dependency behavior
        $this->searchContext = new SearchContext();
        $this->mainQuery = $this->prophesize(Query::class);
        $this->mainQuery->addAggregation(Argument::any())->willReturn($this->prophesize(Query::class)->reveal());
        $this->searchContext->setQuery($this->mainQuery->reveal());
    }

    /** @test */
    public function handle_must_add_aggregations_to_query(): void
    {
        $aggregate1 = new Aggregate('test-field', 'test-aggregate1');
        $aggregate2 = new Aggregate('test-field', 'test-aggregate2');
        $this->searchContext->addAggregation($aggregate1);
        $this->searchContext->addAggregation($aggregate2);

        $terms = $this->prophesize(Terms::class);
        $this->converter->convert($aggregate1)->willReturn($terms->reveal());
        $this->converter->convert($aggregate2)->willReturn($terms->reveal());

        // act
        $this->handler->handle($this->searchContext);

        $this->mainQuery->addAggregation($terms->reveal())
            ->shouldHaveBeenCalledTimes(2);
    }
}
