<?php

namespace PhpOffice\PhpSpreadsheet\Helper\NumberFormat;

class CurrencySymbolLookup
{
    protected static $currencySymbols = [
        'AFN' => '؋',   // Afghani
        'ALL' => 'Lek', // Albanian Lek
        'ARS' => '$',   // Argentine Peso
        'AUD' => '$',   // Australian Dollar
        'CAD' => 'CA$', // Canadian Dollars
        'DKK' => 'kr.', // Danish Krone
        'EUR' => '€',   // Euro
        'GBP' => '£',   // Pounds Sterling
        'USD' => '$',   // US Dollars
    ];

    public static function lookup(string $currencyCode): ?string
    {
        $currencyCode = strtoupper($currencyCode);

        return array_key_exists($currencyCode, self::$currencySymbols) ? self::$currencySymbols[$currencyCode] : null;
    }
}
