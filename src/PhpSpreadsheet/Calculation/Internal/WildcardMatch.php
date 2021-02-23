<?php

namespace PhpOffice\PhpSpreadsheet\Calculation\Internal;

class WildcardMatch
{
    private const SEARCH_SET = [
        '/([^~])(\*)/ui',
        '/~\*/ui',
        '/([^~])(\?)/ui',
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
        return preg_replace(self::SEARCH_SET, self::REPLACEMENT_SET, $wildcard);
    }

    public static function compare($value, string $wildcard): bool
    {
        if ($value === '') {
            return true;
        }

        return (bool) preg_match("/{$wildcard}/ui", $value);
    }
}
