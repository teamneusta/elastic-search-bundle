<?php declare(strict_types=1);

namespace Neusta\ElasticSearchBundle\Tests\Search\Handler\PreSearch;

use Elastica\Query;
use Elastica\Response;
use Elastica\ResultSet;
use JoliCode\Elastically\Index;
use Neusta\ElasticSearchBundle\Handler\PreSearch\AddSuggestions;
use Neusta\ElasticSearchBundle\SearchContext;
use PHPUnit\Framework\TestCase;
use Prophecy\Argument;
use Prophecy\PhpUnit\ProphecyTrait;

class AddSuggestionsTest extends TestCase
{
    use ProphecyTrait;

    private AddSuggestions $handler;

    protected function setUp(): void
    {
        $this->handler = new AddSuggestions();
    }

    public function testHandle(): void
    {
        $searchContext = new SearchContext();
        $searchContext->setSearchTerm('suchwort');

        $index = $this->prophesize(Index::class);
        $result = $this->prophesize(ResultSet::class);
        $response = $this->prophesize(Response::class);
        $response->getData()->willReturn(['ergebnis']);

        $result->getResponse()->willReturn($response->reveal());

        $index->search(Argument::type(Query::class))->willReturn($result->reveal());
        $searchContext->setIndex($index->reveal());

        $this->handler->handle($searchContext);

        self::assertNotEmpty($searchContext->getSuggestions());
        self::assertSame(['ergebnis'], $searchContext->getSuggestions());
    }
}
