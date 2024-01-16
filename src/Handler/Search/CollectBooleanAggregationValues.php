<?php declare(strict_types=1);

namespace Neusta\ElasticSearchBundle\Handler\Search;

use Elastica\Exception\InvalidException;
use Neusta\ElasticSearchBundle\Formatter\Formatter;
use Neusta\ElasticSearchBundle\Handler\SearchHandler;
use Neusta\ElasticSearchBundle\Model\Aggregate\AggregateItem;
use Neusta\ElasticSearchBundle\Model\Aggregate\BooleanAggregate;
use Neusta\ElasticSearchBundle\SearchContext;

class CollectBooleanAggregationValues implements SearchHandler
{
    public function __construct(
        private string $filterTermsName,
        private Formatter $formatter,
    ) {
    }

    public function handle(SearchContext $searchContext): void
    {
        $resultSet = $searchContext->getResultSet();

        $aggregate = null;
        if ($searchContext->hasAggregation($this->filterTermsName)) {
            $aggregate = $searchContext->getAggregationByKey($this->filterTermsName);
        }

        if (!$aggregate instanceof BooleanAggregate) {
            return;
        }

        try {
            /** @var array<string, mixed> $resultSetAggregation */
            $resultSetAggregation = $resultSet->getAggregation($this->filterTermsName);
        } catch (InvalidException) {
            return;
        }

        foreach ($resultSetAggregation['buckets'] ?? [] as $bucket) {
            $value = $bucket['key'];
            if (!empty($value)) {
                $aggregateItem = new AggregateItem($value, $bucket['doc_count'], (string) $this->formatter->format($value));
                $aggregate->addAggregateItem($aggregateItem);
            }
        }
    }
}
