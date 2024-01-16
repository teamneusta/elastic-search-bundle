<?php declare(strict_types=1);

namespace Neusta\ElasticSearchBundle\Tests\Search\Builder;

use Elastica\Query;
use Neusta\ElasticSearchBundle\Builder\FilterBuilder;
use Neusta\ElasticSearchBundle\Factory\QueryFactory;
use Neusta\ElasticSearchBundle\Formatter\Formatter;
use Neusta\ElasticSearchBundle\Model\Filter\Filter;
use Neusta\ElasticSearchBundle\Model\Filter\MultiselectFilter;
use Neusta\ElasticSearchBundle\Model\Filter\RangeFilter;
use PHPUnit\Framework\TestCase;
use Prophecy\Argument;
use Prophecy\PhpUnit\ProphecyTrait;
use Prophecy\Prophecy\ObjectProphecy;

class FilterBuilderTest extends TestCase
{
    use ProphecyTrait;

    private FilterBuilder $filterBuilder;

    private ObjectProphecy|Formatter $formatter;
    private ObjectProphecy|QueryFactory $queryFactory;

    protected function setUp(): void
    {
        // mock dependencies
        $this->queryFactory = $this->prophesize(QueryFactory::class);
        $this->formatter = $this->prophesize(Formatter::class);

        // create test object
        $this->filterBuilder = new FilterBuilder(
            $this->queryFactory->reveal(),
            ['field-name' => $this->formatter->reveal()]
        );

        // mock dependency behavior
        $this->formatter->format(10.0)->willReturn(10);
        $this->formatter->format(100.0)->willReturn(100);
    }

    /** @test */
    public function do_nothing_when_filter_is_not_active(): void
    {
        $filter = (new MultiselectFilter('field-name', 'test-label'))
            ->setActive(false);

        $this->filterBuilder->addFilterToQuery($filter, $this->prophesize(Query::class)->reveal());

        $this->queryFactory->addMustPostFilterQuery(Argument::cetera())
            ->shouldNotHaveBeenCalled();
    }

    /** @test */
    public function do_nothing_when_filter_has_unknown_type(): void
    {
        $filter = new Filter('unknown-type', 'field-name', 'test-label');
        $filter->setActive(true);

        $this->filterBuilder->addFilterToQuery($filter, $this->prophesize(Query::class)->reveal());

        $this->queryFactory->addMustPostFilterQuery(Argument::cetera())
            ->shouldNotHaveBeenCalled();
    }

    /** @test */
    public function addFilterToQuery_must_add_multiselect_filter(): void
    {
        $filter = (new MultiselectFilter('field-name', 'test-label'))
            ->setActive(true)
            ->setSelectedValues(['test-value-1', 'test-value-2']);

        $query = $this->prophesize(Query::class);

        $this->queryFactory->createTermsQuery('field-name', ['test-value-1', 'test-value-2'])
            ->willReturn($this->prophesize(Query\Terms::class)->reveal());

        // act
        $this->filterBuilder->addFilterToQuery($filter, $query->reveal());

        $this->queryFactory
            ->createTermsQuery('field-name', ['test-value-1', 'test-value-2'])
            ->shouldHaveBeenCalled();
        $this->queryFactory->addMustPostFilterQuery(Argument::cetera())
            ->shouldHaveBeenCalled();
    }

    /** @test */
    public function addFilterToQuery_must_add_range_filter(): void
    {
        $filter = (new RangeFilter('field-name', 'test-label'))
            ->setActive(true)
            ->setSelectedMin(10.0)
            ->setSelectedMax(100.0);

        $query = $this->prophesize(Query::class);

        $this->queryFactory->createClosedRangeQuery('field-name', 10, 100)
            ->willReturn($this->prophesize(Query\Range::class)->reveal());

        // act
        $this->filterBuilder->addFilterToQuery($filter, $query->reveal());

        $this->queryFactory
            ->createClosedRangeQuery('field-name', 10, 100)
            ->shouldHaveBeenCalled();
        $this->queryFactory->addMustPostFilterQuery(Argument::cetera())
            ->shouldHaveBeenCalled();
    }
}
