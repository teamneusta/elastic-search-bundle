<?php declare(strict_types=1);

namespace Neusta\ElasticSearchBundle\Model\Filter;

class Filter implements FilterInterface
{
    /** @var array<FilterValue> */
    protected array $filterValues = [];

    protected bool $active = false;

    protected int $activeCount = 0;

    public function __construct(
        protected string $type,
        protected string $fieldName,
        protected string $label,
    ) {
    }

    public function getType(): string
    {
        return $this->type;
    }

    public function getFieldName(): string
    {
        return $this->fieldName;
    }

    public function getLabel(): string
    {
        return $this->label;
    }

    public function isActive(): bool
    {
        return $this->active;
    }

    public function setActive(bool $active): static
    {
        $this->active = $active;

        return $this;
    }

    public function getActiveCount(): int
    {
        return $this->activeCount;
    }

    public function setActiveCount(int $activeCount): static
    {
        $this->activeCount = $activeCount;

        return $this;
    }

    /**
     * @return array<FilterValue>
     */
    public function getFilterValues(): array
    {
        return $this->filterValues;
    }

    /**
     * @param array<FilterValue> $filterValues
     */
    public function setFilterValues(array $filterValues): void
    {
        $this->filterValues = $filterValues;
    }

    public function addFilterValue(FilterValue $filterValue): static
    {
        $this->filterValues[] = $filterValue;

        return $this;
    }

    public function cleanUpSelectedValues(): void
    {
        // nothing to do.
    }
}
