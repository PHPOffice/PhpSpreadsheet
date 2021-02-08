<?php

namespace PhpOffice\PhpSpreadsheetTests\Helper\NumberFormat;

use PhpOffice\PhpSpreadsheet\Helper\NumberFormat\CurrencyLookup;
use PHPUnit\Framework\TestCase;

class CurrencyLookupTest extends TestCase
{
    /**
     * @dataProvider countryCodeLookup
     *
     * @param $expectedResult
     * @param $countryCode
     */
    public function testLookup(string $countryCode, string $expectedResult): void
    {
        $currencyCode = CurrencyLookup::lookup($countryCode);
        self::assertSame($expectedResult, $currencyCode);
    }

    public function countryCodeLookup()
    {
        return [
            ['US', 'USD'],
            ['UK', 'GBP'],
            ['NL', 'EUR'],
        ];
    }

    public function testLookupInvalidCode(): void
    {
        $currencyCode = CurrencyLookup::lookup('ZZ');
        self::assertNull($currencyCode);
    }
}
