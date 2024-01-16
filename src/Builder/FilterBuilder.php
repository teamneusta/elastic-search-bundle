<?php declare(strict_types=1);

namespace Neusta\ElasticSearchBundle\Builder;

use Elastica\Query;
use Neusta\ElasticSearchBundle\Factory\QueryFactory;
use Neusta\ElasticSearchBundle\Formatter\Formatter;
use Neusta\ElasticSearchBundle\Model\Filter\BooleanFilter;
use Neusta\ElasticSearchBundle\Model\Filter\FilterInterface;
use Neusta\ElasticSearchBundle\Model\Filter\MultiselectFilter;
use Neusta\ElasticSearchBundle\Model\Filter\RangeFilter;

class FilterBuilder
{
    /**
     * @param array<string, Formatter> $formatters
     */
    public function __construct(
        private QueryFactory $queryFactory,
        private array $formatters,
    ) {
    }

    public function addFilterToQuery(FilterInterface $filter, Query $query): void
    {
        if (!$filter->isActive()) {
            return;
        }

        if ($filter instanceof MultiselectFilter) {
            $this->addMultiselectFilter($filter, $query);
        }
        if ($filter instanceof RangeFilter) {
            $this->addRangeFilter($filter, $query);
        }
        if ($filter instanceof BooleanFilter) {
            $this->addBooleanFilter($filter, $query);
        }
    }

    protected function addBooleanFilter(BooleanFilter $filter, Query $query): void
    {
        $termsQuery = $this->queryFactory->createTermsQuery($filter->getFieldName(), [0 => true]);
        $this->queryFactory->addMustPostFilterQuery($query, $termsQuery);
    }

    protected function addMultiselectFilter(MultiselectFilter $filter, Query $query): void
    {
        if (\is_array($filter->getSelectedValues())) {
            $termsQuery = $this->queryFactory->createTermsQuery(
                $filter->getFieldName(),
                array_values($filter->getSelectedValues()),
            );
            $this->queryFactory->addMustPostFilterQuery($query, $termsQuery);
        }
    }

    protected function addRangeFilter(RangeFilter $filter, Query $query): void
    {
        $min = $filter->getSelectedMin();
        $max = $filter->getSelectedMax();

        $closedRangeQuery = $this->queryFactory->createClosedRangeQuery(
            $filter->getFieldName(),
            $this->formatters[$filter->getFieldName()]->format($min),
            $this->formatters[$filter->getFieldName()]->format($max),
        );

        $this->queryFactory->addMustPostFilterQuery($query, $closedRangeQuery);
    }
}
