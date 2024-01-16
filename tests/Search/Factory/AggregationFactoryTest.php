<?php declare(strict_types=1);

namespace Neusta\ElasticSearchBundle\Tests\Search\Factory;

use Neusta\ElasticSearchBundle\Exception\UnknownTypeException;
use Neusta\ElasticSearchBundle\Factory\AggregationFactory;
use Neusta\ElasticSearchBundle\Model\Aggregate\MultiselectAggregate;
use Neusta\ElasticSearchBundle\Model\Aggregate\RangeAggregate;
use PHPUnit\Framework\TestCase;

class AggregationFactoryTest extends TestCase
{
    private AggregationFactory $aggregationFactory;

    protected function setUp(): void
    {
        $this->aggregationFactory = new AggregationFactory();
    }

    /** @test */
    public function create_must_return_multiselect_aggregation(): void
    {
        $aggregate = $this->aggregationFactory->create(
            'field',
            'label',
            AggregationFactory::TYPE_MULTISELECT
        );

        self::assertInstanceOf(MultiselectAggregate::class, $aggregate);
        self::assertSame('field', $aggregate->getFieldName());
        self::assertSame('label', $aggregate->getLabel());
    }

    /** @test */
    public function create_must_return_range_aggregation(): void
    {
        $aggregate = $this->aggregationFactory->create(
            'field',
            'label',
            AggregationFactory::TYPE_RANGE
        );

        self::assertInstanceOf(RangeAggregate::class, $aggregate);
        self::assertSame('field', $aggregate->getFieldName());
        self::assertSame('label', $aggregate->getLabel());
    }

    /** @test */
    public function create_must_throw_when_type_is_unknown(): void
    {
        $this->expectException(UnknownTypeException::class);
        $this->expectExceptionMessage("Invalid type 'unknown'. Use either 'range', or 'multiselect'.");

        $this->aggregationFactory->create('field', 'label', 'unknown');
    }
}
