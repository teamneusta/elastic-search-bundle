<?php declare(strict_types=1);

namespace Neusta\ElasticSearchBundle\Handler\Search;

use Elastica\Exception\InvalidException;
use Neusta\ElasticSearchBundle\Exception\FilterWithoutAggregationException;
use Neusta\ElasticSearchBundle\Formatter\Formatter;
use Neusta\ElasticSearchBundle\Handler\SearchHandler;
use Neusta\ElasticSearchBundle\Model\Filter\BooleanFilter;
use Neusta\ElasticSearchBundle\Model\Filter\FilterValue;
use Neusta\ElasticSearchBundle\SearchContext;

class CollectBooleanFilterValues implements SearchHandler
{
    public function __construct(
        private string $filterTermsName,
        private Formatter $formatter,
    ) {
    }

    public function handle(SearchContext $searchContext): void
    {
        $filter = null;
        if ($searchContext->hasFilter($this->filterTermsName)) {
            $filter = $searchContext->getFilterByKey($this->filterTermsName);
        }

        if (!$filter instanceof BooleanFilter) {
            return;
        }

        $resultSet = $searchContext->getResultSet();

        try {
            /** @var array<string, mixed> $resultSetAggregation */
            $resultSetAggregation = $resultSet->getAggregation($this->filterTermsName);
        } catch (InvalidException) {
            throw new FilterWithoutAggregationException(sprintf(
                'You can not collect (possible) filter values without associated aggregation on %s',
                $this->filterTermsName,
            ));
        }

        $buckets = $resultSetAggregation['buckets'];

        foreach ($buckets as $bucket) {
            $value = $bucket['key'];
            if ('' !== $value) {
                $filter->addFilterValue(new FilterValue($value, $bucket['doc_count'], (string) $this->formatter->format($value)));
            }
        }
    }
}
