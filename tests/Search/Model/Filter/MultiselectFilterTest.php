<?php declare(strict_types=1);

namespace Neusta\ElasticSearchBundle\Tests\Search\Model\Filter;

use Neusta\ElasticSearchBundle\Model\Filter\FilterValue;
use Neusta\ElasticSearchBundle\Model\Filter\MultiselectFilter;
use PHPUnit\Framework\TestCase;

class MultiselectFilterTest extends TestCase
{
    private MultiselectFilter $filter;

    public function sampleSelectedValues()
    {
        yield 'no selected values' => [
            [], 0, false, 0,
        ];
        yield 'wrong selected value' => [
            ['l'], 0, false, 0,
        ];
        yield 'single selected value' => [
            ['xl'], 1, true, 1,
        ];
    }

    protected function setUp(): void
    {
        $this->filter = new MultiselectFilter('field-name', 'label');
        $this->filter->setActive(true);
        $this->filter->setActiveCount(0);
        $this->filter->addFilterValue(new FilterValue('xl', 1, 'size'));
    }

    /**
     * @test
     *
     * @param array<string> $selectedValues
     *
     * @dataProvider sampleSelectedValues
     */
    public function cleanUpSelectedValues(
        array $selectedValues,
        int $numberOfSelectedValues,
        bool $isActive,
        int $activeCount
    ): void {
        $emptySelectedValues = $selectedValues;

        $this->filter->setSelectedValues($emptySelectedValues);

        // act
        $this->filter->cleanUpSelectedValues();

        self::assertCount($numberOfSelectedValues, $this->filter->getSelectedValues());
        self::assertSame($isActive, $this->filter->isActive());
        self::assertSame($activeCount, $this->filter->getActiveCount());
    }
}
