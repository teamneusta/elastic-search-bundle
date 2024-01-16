<?php declare(strict_types=1);

use Finder\Filter\Filter;
use Neusta\ElasticSearchBundle\Index\Repository\ElasticRepository;
use ONGR\ElasticsearchBundle\Result\Aggregation\AggregationValue;
use ONGR\ElasticsearchBundle\Result\DocumentIterator;
use ONGR\ElasticsearchDSL\Query\Compound\BoolQuery;
use ONGR\ElasticsearchDSL\Search;
use ONGR\ElasticsearchDSL\Sort\FieldSort;
use Webmozart\Assert\Assert;

class ArchiveFinder
{
    private const DEFAULT_PAGE = 1;
    private const DEFAULT_SIZE = 15;

    private ElasticRepository $repository;
    private iterable $filter;
    private iterable $sort;

    /**
     * @param Filter[]    $filter
     * @param FieldSort[] $sort
     */
    public function __construct(ElasticRepository $repository, iterable $filter, iterable $sort)
    {
        $this->repository = $repository;
        $this->filter = $filter;
        $this->sort = $sort;
    }

    public function find(string $locale, array $filter, ?int $page, ?int $size): array
    {
        Assert::nullOrGreaterThanEq($page, 1);
        Assert::nullOrGreaterThanEq($size, 1);

        $page ??= self::DEFAULT_PAGE;
        $size ??= self::DEFAULT_SIZE;
        $from = ($page - 1) * $size;

        $documents = $this->findDocuments($locale, $filter, $from, $size);

        return [
            'documents' => $documents,
            'filter' => $this->getFilter(),
            'hits' => \count($documents),
            'pages' => (int) ceil(\count($documents) / $size),
        ];
    }

    private function findDocuments(string $locale, array $options, int $from, int $size): DocumentIterator
    {
        $search = new Search();
        $search->setFrom($from);
        $search->setSize($size);

        $options['locale'] = $locale;

        foreach ($this->filter as $filter) {
            if ($query = $filter->getFilter($options)) {
                $search->addQuery($query, BoolQuery::FILTER);
            }
        }

        foreach ($this->sort as $sort) {
            $search->addSort($sort);
        }

        return $this->repository->find($search);
    }

    /**
     * @return AggregationValue[]
     */
    private function getFilter(): array
    {
        $search = new Search();
        $search->setSize(0);

        foreach ($this->filter as $filter) {
            if ($aggregation = $filter->getAggregation()) {
                $search->addAggregation($aggregation);
            }
        }

        return $this->repository->find($search)->getAggregations();
    }
}
