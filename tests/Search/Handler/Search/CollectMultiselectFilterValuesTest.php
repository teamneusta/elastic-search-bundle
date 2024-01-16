<?php declare(strict_types=1);

namespace Neusta\ElasticSearchBundle\Tests\Search\Handler\Search;

use Elastica\Exception\InvalidException;
use Elastica\ResultSet;
use Neusta\ElasticSearchBundle\Exception\FilterWithoutAggregationException;
use Neusta\ElasticSearchBundle\Formatter\Formatter;
use Neusta\ElasticSearchBundle\Handler\Search\CollectMultiselectFilterValues;
use Neusta\ElasticSearchBundle\Model\Filter\FilterInterface;
use Neusta\ElasticSearchBundle\Model\Filter\MultiselectFilter;
use Neusta\ElasticSearchBundle\SearchContext;
use Neusta\ElasticSearchBundle\Sorter\IdemSorter;
use Neusta\ElasticSearchBundle\Sorter\Sorter;
use PHPUnit\Framework\TestCase;
use Prophecy\Argument;
use Prophecy\PhpUnit\ProphecyTrait;
use Prophecy\Prophecy\ObjectProphecy;

class CollectMultiselectFilterValuesTest extends TestCase
{
    use ProphecyTrait;

    private CollectMultiselectFilterValues $collector;
    private ObjectProphecy|Formatter $formatter;
    private ObjectProphecy|Sorter $sorter;

    private ObjectProphecy|SearchContext $searchContext;
    private ObjectProphecy|ResultSet $resultSet;
    private ObjectProphecy|MultiselectFilter $multiselectFilter;

    protected function setUp(): void
    {
        $this->searchContext = new SearchContext();

        $this->searchContext->addFilter(new MultiselectFilter('size', 'size'), 'size');

        $this->formatter = $this->prophesize(Formatter::class);
        $this->formatter->format('s')->willReturn('S');
        $this->formatter->format('m')->willReturn('M');
        $this->formatter->format('l')->willReturn('L');

        $this->resultSet = $this->prophesize(ResultSet::class);
        $this->resultSet->getAggregation('size')->willReturn(
            [
                'buckets' => [
                    [
                        'key' => 's',
                        'doc_count' => 1,
                    ],
                    [
                        'key' => 'm',
                        'doc_count' => 2,
                    ],
                    [
                        'key' => 'l',
                        'doc_count' => 3,
                    ],
                ],
            ],
        );

        $this->searchContext->setResultSet($this->resultSet->reveal());
    }

    /**
     * @test
     */
    public function handle_regular_case_without_sorting(): void
    {
        $this->collector = new CollectMultiselectFilterValues(
            'size',
            $this->formatter->reveal(),
            new IdemSorter(),
        );

        $this->collector->handle($this->searchContext);

        self::assertCount(3, $this->searchContext->getFilters()['size']->getFilterValues());
    }

    /**
     * @test
     */
    public function handle_regular_case_with_sorting(): void
    {
        $this->sorter = $this->prophesize(Sorter::class);
        $this->sorter->sort(
            [
                [
                    'key' => 's',
                    'doc_count' => 1,
                ],
                [
                    'key' => 'm',
                    'doc_count' => 2,
                ],
                [
                    'key' => 'l',
                    'doc_count' => 3,
                ],
            ]
        )->willReturn(
            [
                [
                    'key' => 'l',
                    'doc_count' => 3,
                ],
                [
                    'key' => 'm',
                    'doc_count' => 2,
                ],
                [
                    'key' => 's',
                    'doc_count' => 1,
                ],
            ],
        );

        $this->collector = new CollectMultiselectFilterValues(
            'size',
            $this->formatter->reveal(),
            $this->sorter->reveal(),
        );

        $this->collector->handle($this->searchContext);

        self::assertCount(3, $this->searchContext->getFilters()['size']->getFilterValues());
        self::assertEquals('L', $this->searchContext->getFilters()['size']->getFilterValues()[0]->getLabel());
    }

    /**
     * @test
     */
    public function handle_exceptional_case_without_aggregations(): void
    {
        $this->collector = new CollectMultiselectFilterValues(
            'size',
            $this->formatter->reveal(),
            new IdemSorter(),
        );

        $this->resultSet->getAggregation('size')->willThrow(InvalidException::class);

        try {
            $this->collector->handle($this->searchContext);
        } catch (FilterWithoutAggregationException $exception) {
            self::assertEquals('You can not collect (possible) filter values without associated aggregation on size', $exception->getMessage());

            return;
        }
        self::fail('No or wrong exception has been thrown.');
    }

    /**
     * @test
     */
    public function handle_exceptional_case_without_filter(): void
    {
        $this->collector = new CollectMultiselectFilterValues(
            'unknown',
            $this->formatter->reveal(),
            new IdemSorter(),
        );

        $this->collector->handle($this->searchContext);

        self::assertArrayNotHasKey('unknown', $this->searchContext->getFilters());
    }

    /**
     * @test
     */
    public function handle_exceptional_case_with_wrong_filter_type(): void
    {
        $this->collector = new CollectMultiselectFilterValues(
            'wrong_type',
            $this->formatter->reveal(),
            new IdemSorter(),
        );

        $filter = $this->prophesize(FilterInterface::class);
        $this->searchContext->addFilter($filter->reveal(), 'wrong_type');

        $this->collector->handle($this->searchContext);

        $filter->addFilterValue(Argument::any())->shouldNotHaveBeenCalled();
    }
}
