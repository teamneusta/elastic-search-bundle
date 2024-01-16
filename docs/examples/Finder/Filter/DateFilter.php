<?php declare(strict_types=1);

use Carbon\Carbon;
use ONGR\ElasticsearchDSL\Aggregation\AbstractAggregation;
use ONGR\ElasticsearchDSL\BuilderInterface;
use ONGR\ElasticsearchDSL\Query\TermLevel\RangeQuery;

final class DateFilter implements Filter
{
    private const DATE_FORMAT = '!Y-m-d';

    private string $field;
    private ?Carbon $startDate;
    private ?Carbon $endDate;

    public function __construct(string $field, ?string $startDate = null, ?string $endDate = null)
    {
        $this->field = $field;
        $this->startDate = $startDate ? new Carbon($startDate) : null;
        $this->endDate = $endDate ? new Carbon($endDate) : null;
    }

    public function getFilter(array $options): ?BuilderInterface
    {
        $values = $this->createDateFilter($options);

        return $values
            ? new RangeQuery($this->field, $values)
            : null;
    }

    public function getAggregation(): ?AbstractAggregation
    {
        return null;
    }

    private function createDateFilter(array $options): array
    {
        $startDate = $this->startDate;
        $endDate = $this->endDate;

        if (!empty($options['startDate'])) {
            $startDate = max($startDate, Carbon::createFromFormat(self::DATE_FORMAT, $options['startDate']));
        }

        if (!empty($options['endDate'])) {
            $filterEndDate = Carbon::createFromFormat(self::DATE_FORMAT, $options['endDate']);
            $endDate = null !== $endDate ? min($endDate, $filterEndDate) : $filterEndDate;
        }

        return array_filter([
            RangeQuery::GTE => $startDate,
            RangeQuery::LTE => $endDate,
        ]);
    }
}
