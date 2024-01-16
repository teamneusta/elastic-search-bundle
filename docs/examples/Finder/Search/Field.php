<?php declare(strict_types=1);

use Webmozart\Assert\Assert;

final class Field implements \Stringable
{
    private string $name;
    private ?string $subField = null;
    private ?int $boost = null;

    public function __construct(string $name)
    {
        Assert::notEmpty($name);

        $this->name = $name;
    }

    public function __toString(): string
    {
        return $this->asString();
    }

    public function withSubField(string $name): self
    {
        Assert::notEmpty($name);

        $field = clone $this;
        $field->subField = $name;

        return $field;
    }

    public function withBoost(int $boost): self
    {
        Assert::greaterThanEq($boost, 1);

        $field = clone $this;
        $field->boost = $boost;

        return $field;
    }

    public function asString(): string
    {
        $field = $this->name;

        if ($subField = $this->subField) {
            $field .= ".{$subField}";
        }

        if ($boost = $this->boost) {
            $field .= "^{$boost}";
        }

        return $field;
    }
}
