<?php

namespace PhpOffice\PhpSpreadsheet\Calculation\Internal;

class WildcardMatch
{
    private const SEARCH_SET = [
        '~~', // convert double tilde to unprintable value
        '~\\*', // convert tilde backslash asterisk to [*] (matches literal asterisk in regexp)
        '\\*', // convert backslash asterisk to .* (matches string of any length in regexp)
        '~\\?', // convert tilde backslash question to [?] (matches literal question mark in regexp)
        '\\?', // convert backslash question to . (matches one character in regexp)
        "\x1c", // convert original double tilde to single tilde
    ];

    private const REPLACEMENT_SET = [
        "\x1c",
        '[*]',
        '.*',
        '[?]',
        '.',
        '~',
    ];

    public static function wildcard(string $wildcard): string
    {
        // Preg Escape the wildcard, but protecting the Excel * and ? search characters
        return str_replace(self::SEARCH_SET, self::REPLACEMENT_SET, preg_quote($wildcard));
    }

    public static function compare(string $value, string $wildcard): bool
    {
        if ($value === '') {
            return true;
        }

        return (bool) preg_match("/^{$wildcard}\$/mui", $value);
    }
}
