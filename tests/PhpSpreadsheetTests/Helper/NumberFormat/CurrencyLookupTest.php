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
    public function testLookup(string $countryCode, string $expectedResult)
    {
        $currencyCode = CurrencyLookup::lookup($countryCode);
        $this->assertSame($expectedResult, $currencyCode);
    }

    public function countryCodeLookup()
    {
        return [
            ['US', 'USD'],
            ['UK', 'GBP'],
            ['NL', 'EUR'],
        ];
    }

    public function testLookupInvalidCode()
    {
        $currencyCode = CurrencyLookup::lookup('ZZ');
        $this->assertNull($currencyCode);
    }
}

