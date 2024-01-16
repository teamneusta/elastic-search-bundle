<?php declare(strict_types=1);

namespace Neusta\ElasticSearchBundle\Factory;

use Elastica\Aggregation\Terms;
use Elastica\Query;
use Elastica\Query\BoolQuery;
use Neusta\ElasticSearchBundle\Exception\InvalidQueryException;
use Neusta\ElasticSearchBundle\SearchContext;

class QueryFactory
{
    // see https://www.elastic.co/guide/en/elasticsearch/reference/current/index-modules.html#index-max-result-window
    public const MAX_RESULT_WINDOW = 10000;

    public function createSearchQuery(SearchContext $searchContext): Query
    {
        $query = new Query(new BoolQuery());
        if ($searchContext->isActivePagination()) {
            $page = $searchContext->getPage();
            $itemsPerPage = $searchContext->getItemsPerPage();

            $query->setFrom(($page - 1) * $itemsPerPage);
            $query->setSize($itemsPerPage);
        } else {
            $query->setFrom(0);
            $query->setSize(self::MAX_RESULT_WINDOW);

            return $query;
        }

        return $query;
    }

    public function createQueryStringQuery(string $field): Query\QueryString
    {
        return new Query\QueryString($field);
    }

    /**
     * @param array<bool|string|int|array<string>> $values
     */
    public function createTermsQuery(string $field, array $values): Query\Terms
    {
        $result = new Query\Terms($field);
        foreach ($values as $value) {
            if (!\is_array($value)) {
                $result->addTerm($value);
            } else {
                array_map(fn ($v) => $result->addTerm($v), $value);
            }
        }

        return $result;
    }

    public function createClosedRangeQuery(string $field, ?int $minimum, ?int $maximum): Query\Range
    {
        return $this->createRangeQuery($field, $minimum, 'gte', $maximum, 'lte');
    }

    public function createOpenRangeQuery(string $field, ?int $minimum, ?int $maximum): Query\Range
    {
        return $this->createRangeQuery($field, $minimum, 'gt', $maximum, 'lt');
    }

    public function createLeftOpenRangeQuery(string $field, ?int $minimum, ?int $maximum): Query\Range
    {
        return $this->createRangeQuery($field, $minimum, 'gte', $maximum, 'lt');
    }

    public function createRightOpenRangeQuery(string $field, ?int $minimum, ?int $maximum): Query\Range
    {
        return $this->createRangeQuery($field, $minimum, 'gt', $maximum, 'lte');
    }

    /**
     * @throws InvalidQueryException
     */
    public function addMustQuery(Query $searchQuery, Query\AbstractQuery $query): void
    {
        $innerQuery = $searchQuery->getQuery();

        if (!$innerQuery instanceof BoolQuery) {
            throw new InvalidQueryException();
        }

        $innerQuery->addMust($query);
    }

    /**
     * @throws InvalidQueryException
     */
    public function addMustNotQuery(Query $searchQuery, Query\AbstractQuery $query): void
    {
        $innerQuery = $searchQuery->getQuery();

        if (!$innerQuery instanceof BoolQuery) {
            throw new InvalidQueryException();
        }

        $innerQuery->addMustNot($query);
    }

    public function addAggregation(Query $searchQuery, string $termsName, string $field): void
    {
        $elasticaAggregation = new Terms($termsName);
        $elasticaAggregation->setField($field);
        $searchQuery->addAggregation($elasticaAggregation);
    }

    public function addMustPostFilterQuery(Query $searchQuery, Query\AbstractQuery $query): void
    {
        if ($searchQuery->hasParam('post_filter')) {
            $postFilterQuery = $searchQuery->getParam('post_filter');
        } else {
            $postFilterQuery = new BoolQuery();
            $searchQuery->setPostFilter($postFilterQuery);
        }
        $postFilterQuery->addMust($query);
    }

    private function createRangeQuery(
        string $field,
        ?int $minimum,
        string $minimumComparator,
        ?int $maximum,
        string $maximumComparator,
    ): Query\Range {
        $rangeInterval = [];
        if ($minimum) {
            $rangeInterval[$minimumComparator] = $minimum;
        }
        if ($maximum) {
            $rangeInterval[$maximumComparator] = $maximum;
        }

        return new Query\Range($field, $rangeInterval);
    }
}
