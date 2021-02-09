<?php

namespace PhpOffice\PhpSpreadsheetTests\Helper\NumberFormat;

use PhpOffice\PhpSpreadsheet\Helper\NumberFormat\Currency;
use PhpOffice\PhpSpreadsheet\Helper\NumberFormat\Number;
use PHPUnit\Framework\TestCase;

class CurrencyWithIntlTest extends TestCase
{
    public static function setUpBeforeClass(): void
    {
        if (!extension_loaded('intl')) {
            self::markTestSkipped('The Intl extension is not available');
        }
    }

    /**
     * @group intl
     * @dataProvider currencyMaskWithIntlData
     */
    public function testCurrencyMaskWithIntl(string $expectedResult, ...$args): void
    {
        $currencyFormatter = new Currency(...$args);
        $currencyFormatMask = $currencyFormatter->format();
        self::assertSame($expectedResult, $currencyFormatMask);
    }

    public function currencyMaskWithIntlData()
    {
        return [
            'Default' => ['[$$-en-US]#,##0.00'],
            'English, US (Default)' => ['[$$-en-US]#,##0.00', 'en_US'],
            'English, UK/GB' => ['[$£-en-GB]#,##0.00', 'en_GB'],
            'English, IE, Euros' => ['[$€-en-IE]#,##0.00', 'en_IE', 'EUR'],
            'Dutch, Netherlands' => ['[$€-nl-NL]_#,##0.00;[$€-nl-NL]_-#,##0.00', 'nl_NL'],
            'West Frisian, Netherlands' => ['[$€-fy-NL]_#,##0.00;[$€-fy-NL]_#,##0.00-', 'fy_NL'],
            'Spanish, Spain' => ['#,##0.00_[$€-es-ES]', 'es_ES'],
            'English, Canada' => ['[$CA$-en-CA]#,##0.00', 'en_CA'],
            'French, Canada' => ['#,##0.00_[$$CA-fr-CA]', 'fr_CA'],
            'English, Canada, US Dollars' => ['[$$-en-CA]#,##0.00', 'en_CA', 'USD'],
            'French, Canada, US Dollars' => ['#,##0.00_[$$US-fr-CA]', 'fr_CA', 'USD'],
            'Pashto, Afghanistan' => ['#,##0_[$؋-ps-AF]', 'ps_AF'],
        ];
    }

    /**
     * @group intl
     * @dataProvider currencyMaskWithIntlDataManualOverrides
     */
    public function testCurrencyMaskWithIntlManualOverrides(string $expectedResult, string $locale, array $args): void
    {
        $currencyFormatter = new Currency($locale);

        foreach ($args as $methodName => $methodArgs) {
            $currencyFormatter->{$methodName}(...$methodArgs);
        }

        $currencyFormatMask = $currencyFormatter->format();
        self::assertSame($expectedResult, $currencyFormatMask);
    }

    public function currencyMaskWithIntlDataManualOverrides()
    {
        return [
            'Dutch Euro, 3 decimals' => [
                '[$€-nl-NL]_#,##0.000;[$€-nl-NL]_-#,##0.000',
                'nl_NL',
                [
                    'setDecimals' => [3],
                ],
            ],
            'Dutch Euro, trailing currency symbol' => [
                '#,##0.00_[$€-nl-NL];-#,##0.00_[$€-nl-NL]',
                'nl_NL',
                [
                    'setCurrencySymbol' => ['€', Currency::CURRENCY_SYMBOL_TRAILING, Number::NON_BREAKING_SPACE],
                ],
            ],
            'Euro, No decimals, and with trailing negative sign' => [
                '[$€-nl-NL]_#,##0;[$€-nl-NL]_#,##0-',
                'nl_NL',
                [
                    'setDecimals' => [0],
                    'trailingSign' => [true],
                ],
            ],
            'Euro, No decimals, and with trailing negative sign and separator' => [
                '[$€-nl-NL]_#,##0;[$€-nl-NL]_#,##0_-',
                'nl_NL',
                [
                    'setDecimals' => [0],
                    'trailingSign' => [true, Number::NON_BREAKING_SPACE],
                ],
            ],
            'Euro, No decimals, and with trailing positive or negative sign' => [
                '[$€-nl-NL]_#,##0+;[$€-nl-NL]_#,##0-;[$€-nl-NL]_#,##0',
                'nl_NL',
                [
                    'setDecimals' => [0],
                    'trailingSign' => [true],
                    'displayPositiveSign' => [true],
                ],
            ],
        ];
    }
}
