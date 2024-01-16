<?php declare(strict_types=1);

namespace Neusta\ElasticSearchBundle\Model\Filter;

use Neusta\ElasticSearchBundle\Factory\FilterFactory;

class MultiselectFilter extends Filter
{
    /** @var array<string> */
    private array $selectedValues = [];

    public function __construct(
        string $fieldName,
        string $label,
    ) {
        parent::__construct(FilterFactory::TYPE_MULTISELECT, $fieldName, $label);
    }

    /**
     * @return array<string>
     */
    public function getSelectedValues(): array
    {
        return $this->selectedValues;
    }

    /**
     * @param array<string> $selectedValues
     */
    public function setSelectedValues(array $selectedValues): self
    {
        $this->selectedValues = $selectedValues;

        return $this;
    }

    public function cleanUpSelectedValues(): void
    {
        $plainFilterValues = array_map(
            fn (FilterValue $filterValue) => $filterValue->getValue(),
            $this->filterValues,
        );

        $this->selectedValues = array_filter(
            $this->selectedValues,
            // intentionally not type strict, because filter values can be multiple types
            static fn ($item) => \in_array($item, $plainFilterValues, false),
        );
        $this->activeCount = \count($this->selectedValues);
        $this->active = !empty($this->selectedValues);
    }
}
