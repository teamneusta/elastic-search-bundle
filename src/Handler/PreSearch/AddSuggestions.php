<?php declare(strict_types=1);

namespace Neusta\ElasticSearchBundle\Handler\PreSearch;

use Elastica\Query;
use Elastica\Suggest;
use Elastica\Suggest\Completion;
use Neusta\ElasticSearchBundle\Handler\SearchHandler;
use Neusta\ElasticSearchBundle\SearchContext;

// TODO missing unit test
class AddSuggestions implements SearchHandler
{
    public function handle(SearchContext $searchContext): void
    {
        $query = new Query();
        $completion = new Completion('suggest', 'suggestion');
        $completion->setPrefix($searchContext->getSearchTerm());
        $completion->setFuzzy(['fuzziness' => 1]);

        $suggest = new Suggest();
        $suggest->addSuggestion($completion);
        $query->setSuggest($suggest);

        $resultSet = $searchContext->getIndex()->search($query);

        $elasticaResponse = $resultSet->getResponse();
        $searchContext->setSuggestions($elasticaResponse->getData());
    }
}
