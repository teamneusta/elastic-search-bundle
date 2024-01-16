<?php declare(strict_types=1);

namespace Neusta\ElasticSearchBundle\Tests\Search\Model\Filter;

use Neusta\ElasticSearchBundle\Model\Filter\FilterValue;
use Neusta\ElasticSearchBundle\Model\Filter\RangeFilter;
use PHPUnit\Framework\TestCase;

class RangeFilterTest extends TestCase
{
    private RangeFilter $filter;

    protected function setUp(): void
    {
        $this->filter = new RangeFilter('field-name', 'label');
        $this->filter->setActive(true);
        $this->filter->setActiveCount(1);
        $this->filter->setAbsoluteMin(10.0);
        $this->filter->setAbsoluteMax(40.0);
        $this->filter->setSelectedMin(20.0);
        $this->filter->setSelectedMax(30.0);
    }

    /**
     * @test
     *
     * @dataProvider sampleFilterValues
     */
    public function cleanUpSelectedValues(
        ?FilterValue $filterValue,
        float $selectedMin,
        float $selectedMax,
        bool $isActive,
        int $activeCount,
    ): void {
        // filter values of range filter are empty by default
        if ($filterValue) {
            $this->filter->addFilterValue($filterValue);
        }

        // act
        $this->filter->cleanUpSelectedValues();

        self::assertSame($selectedMin, $this->filter->getSelectedMin());
        self::assertSame($selectedMax, $this->filter->getSelectedMax());
        self::assertSame($isActive, $this->filter->isActive());
        self::assertSame($activeCount, $this->filter->getActiveCount());
    }

    public function sampleFilterValues()
    {
        yield 'No filter value' => [
            null, 10.0, 40.0, false, 0,
        ];
        yield 'filter value is outside selected range' => [
            new FilterValue('35.0', 1, 'size'), 10.0, 40.0, false, 0,
        ];
        yield 'filter value is inside selected range' => [
            new FilterValue('25.0', 1, 'size'), 20.0, 30.0, true, 1,
        ];
    }
}
