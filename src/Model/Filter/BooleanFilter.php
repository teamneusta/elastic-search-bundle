<?php declare(strict_types=1);

namespace Neusta\ElasticSearchBundle\Model\Filter;

use Neusta\ElasticSearchBundle\Factory\FilterFactory;

class BooleanFilter extends Filter
{
    public function __construct(
        string $fieldName,
        string $label,
    ) {
        parent::__construct(FilterFactory::TYPE_BOOLEAN, $fieldName, $label);
    }
}
