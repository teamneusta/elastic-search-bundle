<?php declare(strict_types=1);

namespace Neusta\ElasticSearchBundle\Exception;

class UnknownTypeException extends \InvalidArgumentException
{
    public function __construct(string $type)
    {
        parent::__construct("Invalid type '{$type}'. Use either 'range', or 'multiselect'.");
    }
}
