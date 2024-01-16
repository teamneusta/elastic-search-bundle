<?php declare(strict_types=1);

namespace Neusta\ElasticSearchBundle\Tests\Util;

use Neusta\ElasticSearchBundle\Util\SearchTermFormatter;
use PHPUnit\Framework\TestCase;

class SearchTermFormatterTest extends TestCase
{
    public function removeSpecialCharacterProvider(): iterable
    {
        yield '+' => ['', '+'];
        yield '-' => ['', '-'];
        yield '&&' => ['', '&&'];
        yield '||' => ['', '||'];
        yield '>' => ['', '>'];
        yield '<' => ['', '<'];
        yield '!' => ['', '!'];
        yield '(' => ['', '('];
        yield ')' => ['', ')'];
        yield '{' => ['', '{'];
        yield '}' => ['', '}'];
        yield '[' => ['', '['];
        yield ']' => ['', ']'];
        yield '^' => ['', '^'];
        yield '"' => ['', '"'];
        yield '~' => ['', '~'];
        yield '*' => ['', '*'];
        yield '?' => ['', '?'];
        yield ':' => ['', ':'];
        yield '\\' => ['', '\\'];
        yield 'unicode' => ['☠️☠️', '☠️*☠️'];
        yield 'all special characters' => ['', '+-&&||><!(){}[]^"~*?:\\\/'];
        yield 'special characters with text between' => [
            'devserverpofeasyncelasticsearchuilucene',
            'dev+server-po&&fe()[]{}async^!elastic~search?ui:\\lucene<>',
        ];
    }

    /**
     * @test
     *
     * @dataProvider removeSpecialCharacterProvider
     */
    public function sanitizeTerm_must_remove_special_characters(string $expectedString, string $specialCharacter): void
    {
        $searchTextFormatter = new SearchTermFormatter();

        self::assertSame($expectedString, $searchTextFormatter->sanitizeTerm($specialCharacter));
    }

    public function replaceSpecialCharacterProvider(): iterable
    {
        yield '+' => ['\\+', '+'];
        yield '-' => ['\\-', '-'];
        yield '&&' => ['\\&&', '&&'];
        yield '||' => ['\\||', '||'];
        yield '>' => ['', '>'];
        yield '<' => ['', '<'];
        yield '!' => ['\\!', '!'];
        yield '(' => ['\\(', '('];
        yield ')' => ['\\)', ')'];
        yield '{' => ['\\{', '{'];
        yield '}' => ['\\}', '}'];
        yield '[' => ['\\[', '['];
        yield ']' => ['\\]', ']'];
        yield '^' => ['\\^', '^'];
        yield '"' => ['\\"', '"'];
        yield '~' => ['\\~', '~'];
        yield '*' => ['\\*', '*'];
        yield '?' => ['\\?', '?'];
        yield ':' => ['\\:', ':'];
        yield '\\' => ['\\\\', '\\'];
        yield 'unicode' => ['☠️\\*☠️', '☠️*☠️'];
        yield 'all special characters' => [
            '\\+\\-\\&&\\||\\!\\(\\)\\{\\}\\[\\]\\^\\"\\~\\*\\?\\:\\\\\/',
            '+-&&||><!(){}[]^"~*?:\\/',
        ];
        yield 'special characters with text between' => [
            'dev\\+server\\-po\\&&\\||fe\\(\\)\[\\]\{\\}async\\^\\!elastic\\~search\\?ui\\:\\\\lucene',
            'dev+server-po&&||fe()[]{}async^!elastic~search?ui:\\lucene<>',
        ];
    }

    /**
     * @test
     *
     * @dataProvider replaceSpecialCharacterProvider
     */
    public function escapeTerm_must_remove_special_characters(string $expectedString, string $specialCharacter): void
    {
        $searchTextFormatter = new SearchTermFormatter();

        self::assertSame($expectedString, $searchTextFormatter->escapeTerm($specialCharacter));
    }
}
