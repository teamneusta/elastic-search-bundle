<?php declare(strict_types=1);

namespace Neusta\ElasticSearchBundle\Handler\Search;

use Elastica\Exception\InvalidException;
use Neusta\ElasticSearchBundle\Formatter\Formatter;
use Neusta\ElasticSearchBundle\Handler\SearchHandler;
use Neusta\ElasticSearchBundle\Model\Filter\FilterValue;
use Neusta\ElasticSearchBundle\Model\Filter\RangeFilter;
use Neusta\ElasticSearchBundle\SearchContext;

class CollectRangeFilterValues implements SearchHandler
{
    public function __construct(
        private string $filterTermsName,
        private Formatter $valueFormatter,
        private Formatter $labelFormatter,
    ) {
    }

    public function handle(SearchContext $searchContext): void
    {
        $resultSet = $searchContext->getResultSet();

        /** @var RangeFilter $filter */
        $filter = null;
        if ($searchContext->hasFilter($this->filterTermsName)) {
            $filter = $searchContext->getFilterByKey($this->filterTermsName);
        }

        if (!$filter instanceof RangeFilter) {
            return;
        }

        try {
            /** @var array<string, mixed> $resultSetAggregation */
            $resultSetAggregation = $resultSet->getAggregation($this->filterTermsName);
        } catch (InvalidException) {
            $errorMessage = sprintf(
                'You can not collect (possible) filter values without associated aggregation on %s',
                $this->filterTermsName,
            );
            // TODO specific SearchException
            throw new \Exception($errorMessage);
        }

        $min = \PHP_INT_MAX;
        $max = \PHP_INT_MIN;
        foreach ($resultSetAggregation['buckets'] ?? [] as $bucket) {
            $value = $bucket['key'];
            $filter->addFilterValue(new FilterValue(
                $this->valueFormatter->format($value),
                $bucket['doc_count'],
                (string) $this->labelFormatter->format($value))
            );
            $min = min((int) $value, $min);
            $max = max((int) $value, $max);
        }

        $filter->setAbsoluteMin($this->valueFormatter->format($min));
        $filter->setSelectedMin(max($filter->getSelectedMin(), $filter->getAbsoluteMin()));
        $filter->setAbsoluteMax($this->valueFormatter->format($max));
        $filter->setSelectedMax(min($filter->getSelectedMax(), $filter->getAbsoluteMax()));
    }
}
