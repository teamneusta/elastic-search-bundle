<?php declare(strict_types=1);

use ONGR\ElasticsearchDSL\Aggregation\AbstractAggregation;
use ONGR\ElasticsearchDSL\Aggregation\Bucketing\TermsAggregation;
use ONGR\ElasticsearchDSL\BuilderInterface;
use ONGR\ElasticsearchDSL\Query\TermLevel\TermQuery;

final class TermFilter implements Filter
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
        $value = $options[$this->name] ?? null;

        return $value
            ? new TermQuery($this->field, $value)
            : null;
    }

    public function getAggregation(): ?AbstractAggregation
    {
        return (new TermsAggregation($this->name, $this->field))->addParameter('size', 1000);
    }
}
