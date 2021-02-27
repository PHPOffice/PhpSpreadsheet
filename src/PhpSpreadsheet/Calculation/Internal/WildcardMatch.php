<?php

namespace PhpOffice\PhpSpreadsheet\Calculation\Internal;

class WildcardMatch
{
    private const SEARCH_SET = [
        '/(?<!~)\*/ui',
        '/~\*/ui',
        '/(?<!~)\?/ui',
        '/~\?/ui',
    ];

    private const REPLACEMENT_SET = [
        '${1}.*',
        '\*',
        '${1}.',
        '\?',
    ];

    public static function wildcard(string $wildcard): string
    {
        // Preg Escape the wildcard, but protecting the Excel * and ? search characters
        $wildcard = str_replace(['*', '?'], [0x1A, 0x1B], $wildcard);
        $wildcard = preg_quote($wildcard);
        $wildcard = str_replace([0x1A, 0x1B], ['*', '?'], $wildcard);

        return preg_replace(self::SEARCH_SET, self::REPLACEMENT_SET, $wildcard);
    }

    public static function compare($value, string $wildcard): bool
    {
        if ($value === '') {
            return true;
        }

        return (bool) preg_match("/^{$wildcard}\$/mui", $value);
    }
}
