<?php declare(strict_types=1);

namespace Neusta\ElasticSearchBundle\Exception;

class InvalidQueryException extends \InvalidArgumentException
{
    public function __construct()
    {
        parent::__construct('Invalid query');
    }
}
