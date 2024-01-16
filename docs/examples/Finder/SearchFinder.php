<?php declare(strict_types=1);

use AppBundle\Search\Finder\Search\Fields;
use Elasticsearch\Client;
use Finder\Filter\Filter;
use Finder\Search\Field;
use Neusta\ElasticSearchBundle\Examples\Finder\ResultsTransformer;
use Neusta\ElasticSearchBundle\Examples\Finder\TypeProvider;
use Neusta\ElasticSearchBundle\Examples\Permission\User;
use ONGR\ElasticsearchDSL\BuilderInterface;
use ONGR\ElasticsearchDSL\Highlight\Highlight;
use ONGR\ElasticsearchDSL\Query\Compound\BoolQuery;
use ONGR\ElasticsearchDSL\Query\FullText\MultiMatchQuery;
use ONGR\ElasticsearchDSL\Query\MatchAllQuery;
use ONGR\ElasticsearchDSL\Search;
use Webmozart\Assert\Assert;

class SearchFinder
{
    public const DEFAULT_PAGE = 1;
    public const DEFAULT_SIZE = 15;

    private Client $client;
    private TypeProvider $typeProvider;
    private iterable $filter;
    private ResultsTransformer $resultsTransformer;

    /**
     * @param Filter[] $filter
     */
    public function __construct(
        Client $client,
        TypeProvider $typeProvider,
        iterable $filter,
        ResultsTransformer $resultsTransformer
    ) {
        $this->client = $client;
        $this->typeProvider = $typeProvider;
        $this->filter = $filter;
        $this->resultsTransformer = $resultsTransformer;
    }

    public function find(string $locale, string $type, string $term, ?int $page, ?int $size, ?User $user = null): array
    {
        Assert::nullOrGreaterThanEq($page, 1);
        Assert::nullOrGreaterThanEq($size, 1);

        $page ??= self::DEFAULT_PAGE;
        $size ??= self::DEFAULT_SIZE;
        $from = ($page - 1) * $size;

        $indexes = $this->typeProvider->getSearchTypes($user);
        $searches = [];

        foreach ($indexes as $typ => $index) {
            $searches[] = ['index' => $index];
            $searches[] = $type === $typ
                ? $this->buildSearch($user, $locale, $term, $from, $size)->toArray()
                : $this->buildAmountSearch($user, $locale, $term)->toArray();
        }

        $types = array_keys($indexes);
        $result = [];
        foreach ($this->client->msearch(['body' => $searches])['responses'] as $i => $response) {
            $result[] = [
                'type' => $types[$i],
                'results' => $this->resultsTransformer->transformResults($response['hits']['hits']),
                'hits' => $hits = $response['hits']['total']['value'] ?? $response['hits']['total'],
                'pages' => (int) ceil($hits / $size),
            ];
        }

        return $result;
    }

    private function buildSearch(User $user, string $locale, string $term, int $from, int $size): Search
    {
        $search = new Search();
        $search->setSource(['uri', 'breadcrumbs']);
        $search->setFrom($from);
        $search->setSize($size);
        $search->addQuery($this->createQuery($locale, $term));

        foreach ($this->createFilter(['user' => $user, 'locale' => $locale]) as $filter) {
            $search->addQuery($filter, BoolQuery::FILTER);
        }

        $search->addHighlight($this->createHighlight());

        return $search;
    }

    private function buildAmountSearch(User $user, string $locale, string $term): Search
    {
        return $this->buildSearch($user, $locale, $term, 0, 0);
    }

    private function createQuery(string $locale, string $term): BuilderInterface
    {
        if (empty($term)) {
            return new MatchAllQuery();
        }

        $exactFields = array_map('strval', [
            // Employee
            (new Field('user_id'))->withBoost(8),
            ...Fields::fromName('telephone_number', 'mobile')->withSubField('ngram')->withBoost(5),
        ]);

        $fuzzyFields = array_map('strval', [
            // Pages
            ...(new Fields(
                (new Field('title'))->withBoost(7),
                (new Field('teaser'))->withBoost(6),
                (new Field('meta'))->withBoost(5),
                (new Field('content'))->withBoost(1),
            ))->withSubField($locale),

            // Employee
            ...Fields::fromName('given_name', 'surname', 'full_name')->withBoost(8)
                ->withSubField('normalized'),
            (new Field('job_code'))->withBoost(7),
            (new Field('department_description'))->withBoost(6),
            (new Field('experiences'))->withBoost(4),
            (new Field('hobbies'))->withBoost(3),
            (new Field('country'))->withBoost(2),
            (new Field('linguistic_proficiency'))->withBoost(1),
        ]);

        return new BoolQuery([BoolQuery::SHOULD => [
            new MultiMatchQuery(array_merge($exactFields, $fuzzyFields), $term, [
                'boost' => 10,
            ]),
            new MultiMatchQuery($fuzzyFields, $term, [
                'fuzziness' => 'AUTO',
            ]),
        ]]);
    }

    private function createFilter(array $options): iterable
    {
        foreach ($this->filter as $filter) {
            if ($query = $filter->getFilter($options)) {
                yield $query;
            }
        }
    }

    private function createHighlight(): Highlight
    {
        return (new Highlight())
            ->setParameters([
                'number_of_fragments' => 1,
                'fragment_size' => 200,
                'pre_tags' => '<span class="is-highlighted">',
                'post_tags' => '</span>',
            ])
            ->addField('title', ['number_of_fragments' => 0, 'no_match_size' => 200])
            ->addField('teaser')
            ->addField('content');
    }
}
