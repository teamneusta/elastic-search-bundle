<?php declare(strict_types=1);

namespace Neusta\ElasticSearchBundle\Util;

use Elastica\Util;

class SearchTermFormatter
{
    private const ESCAPABLE_CHARACTERS = ['\\', '+', '-', '&&', '||', '!', '(', ')', '{', '}', '[', ']', '^', '"', '~', '*', '?', ':', '/', '<', '>'];

    /**
     * Removes lucene special characters.
     * Similar to \Elastica\Util::escapeTerm, but remove characters instead of escape them.
     */
    public function sanitizeTerm(string $searchTerm): string
    {
        // do not use preg_replace, because a regular expression would not be readable
        return str_replace(self::ESCAPABLE_CHARACTERS, '', $searchTerm);
    }

    public function escapeTerm(string $searchTerm): string
    {
        return Util::escapeTerm($searchTerm);
    }
}
