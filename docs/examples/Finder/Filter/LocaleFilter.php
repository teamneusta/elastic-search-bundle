<?php declare(strict_types=1);

use ONGR\ElasticsearchDSL\Aggregation\AbstractAggregation;
use ONGR\ElasticsearchDSL\BuilderInterface;
use ONGR\ElasticsearchDSL\Query\Compound\BoolQuery;
use ONGR\ElasticsearchDSL\Query\TermLevel\ExistsQuery;
use ONGR\ElasticsearchDSL\Query\TermLevel\TermQuery;

final class LocaleFilter implements Filter
{
    private string $name;
    private string $field;

    public function __construct(string $name, string $field)
    {
        $this->name = $name;
        $this->field = $field;
    }

    public function getFilter(array $options): ?BuilderInterface
    {
        if (!$value = $options[$this->name] ?? null) {
            return null;
        }

        return (new BoolQuery([BoolQuery::SHOULD => [
            // either the field does not exist or is empty (`null` and `[]` *are* empty!)
            new BoolQuery([BoolQuery::MUST_NOT => [new ExistsQuery($this->field)]]),
            // or it contains the given value
            new TermQuery($this->field, $value),
            // ensure one of the queries matches
        ]]))->addParameter('minimum_should_match', 1);
    }

    public function getAggregation(): ?AbstractAggregation
    {
        return null;
    }
}
