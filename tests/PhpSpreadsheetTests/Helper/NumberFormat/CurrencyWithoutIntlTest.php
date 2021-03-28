<?php

namespace PhpOffice\PhpSpreadsheetTests\Helper\NumberFormat;

use PhpOffice\PhpSpreadsheet\Helper\NumberFormat\Currency;
use PhpOffice\PhpSpreadsheet\Helper\NumberFormat\Number;
use PHPUnit\Framework\TestCase;

class CurrencyWithoutIntlTest extends TestCase
{
    public static function setUpBeforeClass(): void
    {
        if (extension_loaded('intl')) {
            self::markTestSkipped('The Intl extension is available, cannot test for fallback');
        }
    }

    /**
     * @group intl
     * @dataProvider currencyMaskWithoutIntlData
     */
    public function testCurrencyMaskWithoutIntl(string $expectedResult, ...$args): void
    {
        $currencyFormatter = new Currency(...$args);
        $currencyFormatMask = $currencyFormatter->format();
        self::assertSame($expectedResult, $currencyFormatMask);
    }

    public function currencyMaskWithoutIntlData()
    {
        return [
            'Default' => ['[$$-en-US]#,##0.00'],
            'English Language, DefaultCountry' => ['[$$-en-US]#,##0.00', 'en'],
            'English, US, Guess Currency' => ['[$$-en-US]#,##0.00', 'en_US'],
            'English, UK/GB, Guess Currency' => ['[$£-en-GB]#,##0.00', 'en_GB'],
            'English, UK/GB, Specify Currency' => ['[$£-en-GB]#,##0.00', 'en_GB', 'GBP'],
            'English, Canada' => ['[$CA$-en-CA]#,##0.00', 'en_CA', 'CAD'],
            'German, Germany, Guess Currency)' => ['[$€-de-DE]#,##0.00', 'de_DE'],
        ];
    }

    /**
     * @group intl
     * @dataProvider currencyMaskWithoutIntlDataManualOverrides
     */
    public function testCurrencyMaskWithoutIntlManualOverrides(string $expectedResult, string $locale, array $args): void
    {
        $currencyFormatter = new Currency($locale);

        foreach ($args as $methodName => $methodArgs) {
            $currencyFormatter->{$methodName}(...$methodArgs);
        }

        $currencyFormatMask = $currencyFormatter->format();
        self::assertSame($expectedResult, $currencyFormatMask);
    }

    public function currencyMaskWithoutIntlDataManualOverrides()
    {
        return [
            'GBP, with Leading Pound Sterling and space separator' => [
                '[$£-en-GB] #,##0.00',
                'en_GB',
                [
                    'setCurrencySymbol' => ['£', Currency::CURRENCY_SYMBOL_LEADING, ' '],
                ],
            ],
            'GBP, with Leading Pound Sterling and no separator' => [
                '[$£-en-GB]#,##0.00',
                'en_GB',
                [
                    'setCurrencySymbol' => ['£', Currency::CURRENCY_SYMBOL_LEADING],
                ],
            ],
            'GBP, with Leading Pound Sterling and separator' => [
                '[$£-en-GB] #,##0.00',
                'en_GB',
                [
                    'setCurrencySymbol' => ['£', Currency::CURRENCY_SYMBOL_LEADING, Number::NON_BREAKING_SPACE],
                ],
            ],
            'GBP, with Trailing Pound Sterling and separator' => [
                '#,##0.00 [$£-en-GB]',
                'en_GB',
                [
                    'setCurrencySymbol' => ['£', Currency::CURRENCY_SYMBOL_TRAILING, Number::NON_BREAKING_SPACE],
                ],
            ],
            'Euro, Integer with Leading Sign and no separator' => [
                '[$€-en-US]#,##0',
                'en_US',
                [
                    'setDecimals' => [0],
                    'setCurrencySymbol' => ['€'],
                ],
            ],
            'Euro, No decimals, and with trailing negative sign' => [
                '[$€-nl-NL]#,##0;[$€-nl-NL]#,##0-',
                'nl_NL',
                [
                    'setDecimals' => [0],
                    'trailingSign' => [true],
                ],
            ],
            'Euro, No decimals, and with trailing positive or negative sign' => [
                '[$€-nl-NL]#,##0+;[$€-nl-NL]#,##0-;[$€-nl-NL]0',
                'nl_NL',
                [
                    'setDecimals' => [0],
                    'trailingSign' => [true],
                    'displayPositiveSign' => [true],
                ],
            ],
        ];
    }

    /**
     * @group intl
     * @dataProvider currencyMaskWithoutIntlColors
     */
    public function testCurrencyMaskWithoutIntlColors(string $expectedResult, string $locale, array $args): void
    {
        $numberFormatter = new Currency($locale);

        foreach ($args as $methodName => $methodArgs) {
            $numberFormatter->{$methodName}(...$methodArgs);
        }

        $numberFormatMask = $numberFormatter->format();
        self::assertSame($expectedResult, $numberFormatMask);
    }

    public function currencyMaskWithoutIntlColors()
    {
        return [
            'NL /Red/' => [
                '[$€-nl-NL]#,##0.00;[Red][$€-nl-NL]-#,##0.00',
                'nl_NL',
                [
                    'setColors' => [null, 'Red'],
                ],
            ],
            'NL Green/Red/Blue' => [
                '[Green][$€-nl-NL]#,##0.00;[Red][$€-nl-NL]-#,##0.00;[Blue][$€-nl-NL]0.00',
                'nl_NL',
                [
                    'setColors' => ['Green', 'Red', 'Blue'],
                ],
            ],
            'ES /Red/' => [
                '[$€-es-ES]#,##0.00;[Red][$€-es-ES]-#,##0.00',
                'es_ES',
                [
                    'setColors' => [null, 'Red'],
                ],
            ],
            'ES Green/Red/Blue' => [
                '[Green][$€-es-ES]#,##0.00;[Red][$€-es-ES]-#,##0.00;[Blue][$€-es-ES]0.00',
                'es_ES',
                [
                    'setColors' => ['Green', 'Red', 'Blue'],
                ],
            ],
            'ES /Red/, Trailing symbol' => [
                '#,##0.00[$€-es-ES];[Red]-#,##0.00[$€-es-ES]',
                'es_ES',
                [
                    'setCurrencySymbol' => ['€', Currency::CURRENCY_SYMBOL_TRAILING],
                    'setColors' => [null, 'Red'],
                ],
            ],
            'ES Green/Red/Blue, Trailing symbol' => [
                '[Green]#,##0.00[$€-es-ES];[Red]-#,##0.00[$€-es-ES];[Blue]0.00[$€-es-ES]',
                'es_ES',
                [
                    'setCurrencySymbol' => ['€', Currency::CURRENCY_SYMBOL_TRAILING],
                    'setColors' => ['Green', 'Red', 'Blue'],
                ],
            ],
        ];
    }
}
