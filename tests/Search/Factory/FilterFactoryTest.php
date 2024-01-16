<?php declare(strict_types=1);

namespace Neusta\ElasticSearchBundle\Tests\Search\Factory;

use Neusta\ElasticSearchBundle\Factory\FilterFactory;
use Neusta\ElasticSearchBundle\Model\Filter\Filter;
use Neusta\ElasticSearchBundle\Model\Filter\MultiselectFilter;
use Neusta\ElasticSearchBundle\Model\Filter\RangeFilter;
use PHPUnit\Framework\TestCase;
use Prophecy\PhpUnit\ProphecyTrait;

class FilterFactoryTest extends TestCase
{
    use ProphecyTrait;

    private FilterFactory $filterFactory;

    protected function setUp(): void
    {
        $this->filterFactory = new FilterFactory();
    }

    /** @test */
    public function create_must_return_multiselect_filter(): void
    {
        $filter = $this->filterFactory->create(
            'field',
            'label',
            FilterFactory::TYPE_MULTISELECT
        );

        self::assertInstanceOf(MultiselectFilter::class, $filter);
        self::assertSame('field', $filter->getFieldName());
        self::assertSame('label', $filter->getLabel());
        self::assertFalse($filter->isActive());
    }

    /** @test */
    public function create_must_return_range_filter(): void
    {
        $filter = $this->filterFactory->create(
            'field',
            'label',
            FilterFactory::TYPE_RANGE
        );

        self::assertInstanceOf(RangeFilter::class, $filter);
        self::assertSame('field', $filter->getFieldName());
        self::assertSame('label', $filter->getLabel());
        self::assertFalse($filter->isActive());
    }

    /** @test */
    public function create_must_return_filter(): void
    {
        $filter = $this->filterFactory->create(
            'field',
            'label',
            'custom-type'
        );

        self::assertInstanceOf(Filter::class, $filter);
        self::assertSame('field', $filter->getFieldName());
        self::assertSame('label', $filter->getLabel());
        self::assertFalse($filter->isActive());
        self::assertSame('custom-type', $filter->getType());
    }
}
