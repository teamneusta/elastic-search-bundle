<?php declare(strict_types=1);

namespace Neusta\ElasticSearchBundle;

use Neusta\ElasticSearchBundle\Handler\SearchHandler;

class SearchService
{
    /**
     * @param array<SearchHandler> $preSearchHandlers
     * @param array<SearchHandler> $searchHandlers
     * @param array<SearchHandler> $postSearchHandlers
     */
    public function __construct(
        private array $preSearchHandlers,
        private array $searchHandlers,
        private array $postSearchHandlers,
    ) {
    }

    public function search(SearchContext $searchContext): void
    {
        foreach ($this->preSearchHandlers as $preSearchHandler) {
            $preSearchHandler->handle($searchContext);
        }
        foreach ($this->searchHandlers as $searchHandler) {
            $searchHandler->handle($searchContext);
        }
        foreach ($this->postSearchHandlers as $postSearchHandler) {
            $postSearchHandler->handle($searchContext);
        }
    }
}
