<?php declare(strict_types=1);

use ONGR\ElasticsearchDSL\Aggregation\AbstractAggregation;
use ONGR\ElasticsearchDSL\BuilderInterface;
use ONGR\ElasticsearchDSL\Query\Compound\BoolQuery;
use ONGR\ElasticsearchDSL\Query\TermLevel\ExistsQuery;
use ONGR\ElasticsearchDSL\Query\TermLevel\TermsQuery;

final class UserTypeFilter implements Filter
{
    private string $field;
    private array $types;

    /**
     * @param string[] $types
     */
    public function __construct(string $field, array $types)
    {
        $this->field = $field;
        $this->types = $types;
    }

    public function getFilter(array $options): ?BuilderInterface
    {
        return (new BoolQuery([BoolQuery::SHOULD => [
            // either the field does not exist or is empty (`null` and `[]` *are* empty!)
            new BoolQuery([BoolQuery::MUST_NOT => [new ExistsQuery($this->field)]]),
            // or it contains the given value
            new TermsQuery($this->field, $this->types),
            // ensure one of the queries matches
        ]]))->addParameter('minimum_should_match', 1);
    }

    public function getAggregation(): ?AbstractAggregation
    {
        return null;
    }
}
