<?php

namespace PhpOffice\PhpSpreadsheetTests\Helper\NumberFormat;

use PhpOffice\PhpSpreadsheet\Helper\NumberFormat\Accounting;
use PhpOffice\PhpSpreadsheet\Helper\NumberFormat\Currency;
use PhpOffice\PhpSpreadsheet\Helper\NumberFormat\Number;
use PHPUnit\Framework\TestCase;

class AccountingWithoutIntlTest extends TestCase
{
    public static function setUpBeforeClass(): void
    {
        if (extension_loaded('intl')) {
            self::markTestSkipped('The Intl extension is available, cannot test for fallback');
        }
    }

    /**
     * @group intl
     * @dataProvider accountingMaskWithoutIntlData
     */
    public function testAccountingMaskWithoutIntl(string $expectedResult, ...$args): void
    {
        $AccountingFormatter = new Accounting(...$args);
        $AccountingFormatMask = $AccountingFormatter->format();
        self::assertSame($expectedResult, $AccountingFormatMask);
    }

    public function accountingMaskWithoutIntlData()
    {
        return [
            'Default' => ['[$$-en-US]#,##0.00'],
            'English Language, DefaultCountry' => ['[$$-en-US]#,##0.00', 'en'],
            'English, US, Guess Accounting' => ['[$$-en-US]#,##0.00', 'en_US'],
            'English, UK/GB, Guess Accounting' => ['[$£-en-GB]#,##0.00', 'en_GB'],
            'English, UK/GB, Specify Accounting' => ['[$£-en-GB]#,##0.00', 'en_GB', 'GBP'],
            'English, Canada' => ['[$CA$-en-CA]#,##0.00', 'en_CA', 'CAD'],
            'German, Germany, Guess Accounting)' => ['[$€-de-DE]#,##0.00', 'de_DE'],
        ];
    }

    /**
     * @group intl
     * @dataProvider accountingMaskWithoutIntlDataManualOverrides
     */
    public function testAccountingMaskWithoutIntlManualOverrides(string $expectedResult, string $locale, array $args): void
    {
        $currencyFormatter = new Accounting($locale);

        foreach ($args as $methodName => $methodArgs) {
            $currencyFormatter->{$methodName}(...$methodArgs);
        }

        $currencyFormatMask = $currencyFormatter->format();
        self::assertSame($expectedResult, $currencyFormatMask);
    }

    public function accountingMaskWithoutIntlDataManualOverrides()
    {
        return [
            'Dutch Euro, 3 decimals' => [
                '[$€-nl-NL]#,##0.000;([$€-nl-NL]#,##0.000)',
                'nl_NL',
                [
                    'setDecimals' => [3],
                    'wrapNegativeValues' => [true],
                ],
            ],
            'Dutch Euro, trailing currency symbol' => [
                '#,##0.00_[$€-nl-NL];(#,##0.00_[$€-nl-NL])',
                'nl_NL',
                [
                    'setCurrencySymbol' => ['€', Currency::CURRENCY_SYMBOL_TRAILING, Number::NON_BREAKING_SPACE],
                    'wrapNegativeValues' => [true],
                ],
            ],
            'Spanish Euro (Trailing currency symbol), No decimals, Negative in brackets' => [
                '#,##0_[$€-es-ES];(#,##0_[$€-es-ES])',
                'es_ES',
                [
                    'setDecimals' => [0],
                    'setCurrencySymbol' => ['€', Currency::CURRENCY_SYMBOL_TRAILING, Number::NON_BREAKING_SPACE],
                    'wrapNegativeValues' => [true],
                ],
            ],
            'Denmark, Krone, Trailing sign, Trailing currency symbol' => [
                '#,##0.00_[$kr.-da-DK];#,##0.00-_[$kr.-da-DK]',
                'da_DK',
                [
                    'trailingSign' => [true],
                    'setCurrencySymbol' => ['kr.', Currency::CURRENCY_SYMBOL_TRAILING, Number::NON_BREAKING_SPACE],
                ],
            ],
        ];
    }
}
