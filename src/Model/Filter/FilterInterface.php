<?php declare(strict_types=1);

namespace Neusta\ElasticSearchBundle\Model\Filter;

interface FilterInterface
{
    public function getType(): string;

    public function getFieldName(): string;

    public function getLabel(): string;

    public function isActive(): bool;

    /** @return $this */
    public function setActive(bool $active): static;

    public function getActiveCount(): int;

    /** @return $this */
    public function setActiveCount(int $activeCount): static;

    public function addFilterValue(FilterValue $filterValue): static;

    /**
     * @return array<FilterValue>
     */
    public function getFilterValues(): array;

    /**
     * @param array<FilterValue> $filterValues
     */
    public function setFilterValues(array $filterValues): void;

    public function cleanUpSelectedValues(): void;
}
