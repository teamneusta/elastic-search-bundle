<?php declare(strict_types=1);

namespace Neusta\ElasticSearchBundle\Model\Aggregate;

class RangeAggregate extends Aggregate
{
    private float $minValue = \PHP_FLOAT_MIN;
    private float $maxValue = \PHP_FLOAT_MAX;
    private float $currentValueMin = \PHP_FLOAT_MIN;
    private float $currentValueMax = \PHP_FLOAT_MAX;

    public function getMinValue(): float
    {
        return $this->minValue;
    }

    public function setMinValue(float $minValue): self
    {
        $this->minValue = $minValue;

        return $this;
    }

    public function getMaxValue(): float
    {
        return $this->maxValue;
    }

    public function setMaxValue(float $maxValue): self
    {
        $this->maxValue = $maxValue;

        return $this;
    }

    public function getCurrentValueMin(): float
    {
        return $this->currentValueMin;
    }

    public function setCurrentValueMin(float $currentValueMin): self
    {
        $this->currentValueMin = $currentValueMin;

        return $this;
    }

    public function getCurrentValueMax(): float
    {
        return $this->currentValueMax;
    }

    public function setCurrentValueMax(float $currentValueMax): self
    {
        $this->currentValueMax = $currentValueMax;

        return $this;
    }
}
