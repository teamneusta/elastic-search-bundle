<?php declare(strict_types=1);

namespace Neusta\ElasticSearchBundle;

use Elastica\Query;
use Elastica\ResultSet;
use JoliCode\Elastically\Index;
use JoliCode\Elastically\Model\Document;
use Neusta\ConverterBundle\Converter\Context\GenericContext;
use Neusta\ElasticSearchBundle\Model\Aggregate\AggregateInterface;
use Neusta\ElasticSearchBundle\Model\Filter\FilterInterface;

class SearchContext extends GenericContext
{
    protected Index $index;

    protected int $page = 1;
    protected int $itemsPerPage = 10;

    protected bool $activePagination = false;

    protected string $searchTerm;
    protected Query $query;

    protected ResultSet $resultSet;
    protected int $totalCount;
    /** @var array<Document> */
    protected array $searchResults;

    /** @var array<string, mixed> Response data array */
    protected array $suggestions;

    /** @var array<FilterInterface> */
    protected array $filters = [];

    /** @var array<AggregateInterface> */
    protected array $aggregations = [];

    public function getIndex(): Index
    {
        return $this->index;
    }

    public function setIndex(Index $index): self
    {
        $this->index = $index;

        return $this;
    }

    public function getPage(): int
    {
        return $this->page;
    }

    public function setPage(int $page): void
    {
        $this->page = $page;
    }

    public function getItemsPerPage(): int
    {
        return $this->itemsPerPage;
    }

    public function setItemsPerPage(int $itemsPerPage): self
    {
        $this->itemsPerPage = $itemsPerPage;

        return $this;
    }

    public function isActivePagination(): bool
    {
        return $this->activePagination;
    }

    public function setActivePagination(bool $activePagination): self
    {
        $this->activePagination = $activePagination;

        return $this;
    }

    public function getSearchTerm(): string
    {
        return $this->searchTerm;
    }

    public function setSearchTerm(string $searchTerm): self
    {
        $this->searchTerm = $searchTerm;

        return $this;
    }

    public function getQuery(): Query
    {
        return $this->query;
    }

    public function setQuery(Query $query): self
    {
        $this->query = $query;

        return $this;
    }

    public function getResultSet(): ResultSet
    {
        return $this->resultSet;
    }

    public function setResultSet(ResultSet $resultSet): self
    {
        $this->resultSet = $resultSet;

        return $this;
    }

    public function getTotalCount(): int
    {
        return $this->totalCount;
    }

    public function setTotalCount(int $totalCount): self
    {
        $this->totalCount = $totalCount;

        return $this;
    }

    /**
     * @return array<Document>
     */
    public function getSearchResults(): array
    {
        return $this->searchResults;
    }

    /**
     * @param array<Document> $searchResults
     */
    public function setSearchResults(array $searchResults): self
    {
        $this->searchResults = $searchResults;

        return $this;
    }

    /**
     * @return array<string, mixed>
     */
    public function getSuggestions(): array
    {
        return $this->suggestions;
    }

    /**
     * @param array<string, mixed> $suggestions
     */
    public function setSuggestions(array $suggestions): self
    {
        $this->suggestions = $suggestions;

        return $this;
    }

    /**
     * @return array<FilterInterface>
     */
    public function getFilters(): array
    {
        return $this->filters;
    }

    public function removeFilterByKey(string $key): void
    {
        unset($this->filters[$key]);
    }

    public function getFilterByKey(string $key): FilterInterface
    {
        return $this->filters[$key];
    }

    public function addFilter(FilterInterface $filter, string $key = ''): self
    {
        if (empty($key)) {
            $key = $filter->getLabel();
        }
        $this->filters[$key] = $filter;

        return $this;
    }

    public function hasFilter(string $key): bool
    {
        return \array_key_exists($key, $this->filters);
    }

    /**
     * @return array<AggregateInterface>
     */
    public function getAggregations(): array
    {
        return $this->aggregations;
    }

    public function getAggregationByKey(string $key): AggregateInterface
    {
        return $this->aggregations[$key];
    }

    public function addAggregation(AggregateInterface $aggregation, string $key = ''): self
    {
        if (empty($key)) {
            $key = $aggregation->getLabel();
        }
        $this->aggregations[$key] = $aggregation;

        return $this;
    }

    public function hasAggregation(string $key): bool
    {
        return \array_key_exists($key, $this->aggregations);
    }
}
