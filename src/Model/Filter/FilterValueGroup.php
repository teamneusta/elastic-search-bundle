<?php declare(strict_types=1);

namespace Neusta\ElasticSearchBundle\Model\Filter;

class FilterValueGroup
{
    private string $label;

    /** @var array<FilterValueGroup> */
    private array $subGroups;

    /** @var array<FilterValue> */
    private array $filterValues;

    public function getLabel(): string
    {
        return $this->label;
    }

    public function setLabel(string $label): self
    {
        $this->label = $label;

        return $this;
    }

    /**
     * @return array<FilterValueGroup>
     */
    public function getSubGroups(): array
    {
        return $this->subGroups;
    }

    public function addSubGroup(self $subGroup): self
    {
        $this->subGroups[] = $subGroup;

        return $this;
    }

    /**
     * @param array<FilterValueGroup> $subGroups
     */
    public function setSubGroups(array $subGroups): self
    {
        $this->subGroups = $subGroups;

        return $this;
    }

    /**
     * @return array<FilterValue>
     */
    public function getFilterValues(): array
    {
        return $this->filterValues;
    }

    public function addFilterValue(FilterValue $filterValue): self
    {
        $this->filterValues[] = $filterValue;

        return $this;
    }
}
