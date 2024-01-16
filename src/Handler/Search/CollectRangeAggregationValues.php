<?php declare(strict_types=1);

namespace Neusta\ElasticSearchBundle\Handler\Search;

use Elastica\Exception\InvalidException;
use Neusta\ElasticSearchBundle\Formatter\Formatter;
use Neusta\ElasticSearchBundle\Handler\SearchHandler;
use Neusta\ElasticSearchBundle\Model\Aggregate\AggregateItem;
use Neusta\ElasticSearchBundle\Model\Aggregate\RangeAggregate;
use Neusta\ElasticSearchBundle\SearchContext;

class CollectRangeAggregationValues implements SearchHandler
{
    public function __construct(
        private string $filterTermsName,
        private Formatter $formatter,
    ) {
    }

    public function handle(SearchContext $searchContext): void
    {
        $min = \PHP_FLOAT_MIN;
        $max = \PHP_FLOAT_MAX;

        $resultSet = $searchContext->getResultSet();

        $aggregate = null;
        if ($searchContext->hasAggregation($this->filterTermsName)) {
            $aggregate = $searchContext->getAggregationByKey($this->filterTermsName);
        }

        if (!$aggregate instanceof RangeAggregate) {
            return;
        }

        try {
            /** @var array<string, mixed> $resultSetAggregation */
            $resultSetAggregation = $resultSet->getAggregation($this->filterTermsName);
        } catch (InvalidException) {
            return;
        }

        foreach ($resultSetAggregation['buckets'] ?? [] as $bucket) {
            $min = ((int) $bucket['key'] > $min) ? (int) ($bucket['key']) : $min;
            $max = ((int) $bucket['key'] < $max) ? (int) ($bucket['key']) : $max;
            $aggregateItem = new AggregateItem((string) $bucket['key'], (int) $bucket['doc_count'], (string) $this->formatter->format($bucket['key']));
            $aggregate->addAggregateItem($aggregateItem);
        }

        if (empty($aggregate->getCurrentValueMin())) {
            $aggregate->setCurrentValueMin($this->formatter->format($min));
        }
        if (empty($aggregate->getCurrentValueMax())) {
            $aggregate->setCurrentValueMax($this->formatter->format($max));
        }
        $aggregate->setMinValue($this->formatter->format($min));
        $aggregate->setMaxValue($this->formatter->format($max));
    }
}
