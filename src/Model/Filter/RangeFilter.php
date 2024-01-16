<?php declare(strict_types=1);

namespace Neusta\ElasticSearchBundle\Model\Filter;

use Neusta\ElasticSearchBundle\Factory\FilterFactory;

class RangeFilter extends Filter
{
    private float $absoluteMin = \PHP_FLOAT_MIN;
    private float $absoluteMax = \PHP_FLOAT_MAX;
    private float $selectedMin = \PHP_FLOAT_MIN;
    private float $selectedMax = \PHP_FLOAT_MAX;

    public function __construct(
        string $fieldName,
        string $label,
    ) {
        parent::__construct(FilterFactory::TYPE_RANGE, $fieldName, $label);
    }

    public function getAbsoluteMin(): float
    {
        return $this->absoluteMin;
    }

    public function setAbsoluteMin(float $absoluteMin): self
    {
        $this->absoluteMin = $absoluteMin;

        return $this;
    }

    public function getAbsoluteMax(): float
    {
        return $this->absoluteMax;
    }

    public function setAbsoluteMax(float $absoluteMax): self
    {
        $this->absoluteMax = $absoluteMax;

        return $this;
    }

    public function getSelectedMin(): float
    {
        return $this->selectedMin;
    }

    public function setSelectedMin(float $selectedMin): self
    {
        $this->selectedMin = $selectedMin;

        return $this;
    }

    public function getSelectedMax(): float
    {
        return $this->selectedMax;
    }

    public function setSelectedMax(float $selectedMax): self
    {
        $this->selectedMax = $selectedMax;

        return $this;
    }

    public function cleanUpSelectedValues(): void
    {
        $isAnyFilterValuePossible = false;
        foreach ($this->filterValues as $filterValue) {
            if ($this->isFilterValueInRangeOfSelectedMinMax($filterValue)) {
                $isAnyFilterValuePossible = true;
                break;
            }
        }

        if (!$isAnyFilterValuePossible) {
            $this->selectedMin = $this->absoluteMin;
            $this->selectedMax = $this->absoluteMax;
            $this->activeCount = 0;
            $this->active = false;
        }
    }

    private function isFilterValueInRangeOfSelectedMinMax(FilterValue $filterValue): bool
    {
        return $filterValue->getValue() >= $this->selectedMin
            && $filterValue->getValue() <= $this->selectedMax;
    }
}
