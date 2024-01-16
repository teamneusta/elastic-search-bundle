<?php declare(strict_types=1);

namespace Neusta\ElasticSearchBundle\Handler;

use Neusta\ElasticSearchBundle\SearchContext;

interface SearchHandler
{
    public function handle(SearchContext $searchContext): void;
}
