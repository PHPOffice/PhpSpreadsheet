<?php

namespace PhpOffice\PhpSpreadsheetTests\Style\NumberFormat\Wizard;

use NumberFormatter;
use PhpOffice\PhpSpreadsheet\Exception;
use PhpOffice\PhpSpreadsheet\Style\NumberFormat\Wizard\Currency;
use PhpOffice\PhpSpreadsheet\Style\NumberFormat\Wizard\Number;
use PHPUnit\Framework\TestCase;

class CurrencyTest extends TestCase
{
    /**
     * @dataProvider providerCurrency
     */
    public function testCurrency(
        string $expectedResult,
        string $currencyCode,
        int $decimals,
        bool $thousandsSeparator,
        bool $currencySymbolPosition,
        bool $currencySymbolSpacing
    ): void {
        $wizard = new Currency($currencyCode, $decimals, $thousandsSeparator, $currencySymbolPosition, $currencySymbolSpacing);
        self::assertSame($expectedResult, (string) $wizard);
    }

    public static function providerCurrency(): array
    {
        return [
            ["\$\u{a0}0", '$', 0, Number::WITHOUT_THOUSANDS_SEPARATOR, Currency::LEADING_SYMBOL, Currency::SYMBOL_WITH_SPACING],
            ["\$\u{a0}#,##0", '$', 0, Number::WITH_THOUSANDS_SEPARATOR, Currency::LEADING_SYMBOL, Currency::SYMBOL_WITH_SPACING],
            ['$#,##0', '$', 0, Number::WITH_THOUSANDS_SEPARATOR, Currency::LEADING_SYMBOL, Currency::SYMBOL_WITHOUT_SPACING],
            ["0.00\u{a0}€", '€', 2, Number::WITHOUT_THOUSANDS_SEPARATOR, Currency::TRAILING_SYMBOL, Currency::SYMBOL_WITH_SPACING],
            ["#,##0.00\u{a0}€", '€', 2, Number::WITH_THOUSANDS_SEPARATOR, Currency::TRAILING_SYMBOL, Currency::SYMBOL_WITH_SPACING],
            ['0.00€', '€', 2, Number::WITHOUT_THOUSANDS_SEPARATOR, Currency::TRAILING_SYMBOL, Currency::SYMBOL_WITHOUT_SPACING],
        ];
    }

    /**
     * @dataProvider providerCurrencyLocale
     */
    public function testCurrencyLocale(
        string $expectedResult,
        string $currencyCode,
        string $locale
    ): void {
        if (class_exists(NumberFormatter::class) === false) {
            self::markTestSkipped('Intl extension is not available');
        }

        $wizard = new Currency($currencyCode);
        $wizard->setLocale($locale);
        self::assertSame($expectedResult, (string) $wizard);
    }

    public static function providerCurrencyLocale(): array
    {
        return [
            ["[\$€-fy-NL]\u{a0}#,##0.00;[\$€-fy-NL]\u{a0}#,##0.00-", '€', 'fy-NL'], // Trailing negative
            ["[\$€-nl-NL]\u{a0}#,##0.00;[\$€-nl-NL]\u{a0}-#,##0.00", '€', 'nl-NL'], // Sign between currency and value
            ["[\$€-nl-BE]\u{a0}#,##0.00;[\$€-nl-BE]\u{a0}-#,##0.00", '€', 'NL-BE'], // Sign between currency and value
            ["#,##0.00\u{a0}[\$€-fr-BE]", '€', 'fr-be'],   // Trailing currency code
            ["#,##0.00\u{a0}[\$€-el-GR]", '€', 'el-gr'],   // Trailing currency code
            ['[$$-en-CA]#,##0.00', '$', 'en-ca'],
            ["#,##0.00\u{a0}[\$\$-fr-CA]", '$', 'fr-ca'],   // Trailing currency code
            ['[$¥-ja-JP]#,##0', '¥', 'ja-JP'], // No decimals
            ["#,##0.000\u{a0}[\$د.ب‎-ar-BH]", 'د.ب‎', 'ar-BH'],  // 3 decimals
        ];
    }

    /**
     * @dataProvider providerCurrencyLocaleNoDecimals
     */
    public function testCurrencyLocaleNoDecimals(
        string $expectedResult,
        string $currencyCode,
        string $locale
    ): void {
        if (class_exists(NumberFormatter::class) === false) {
            self::markTestSkipped('Intl extension is not available');
        }

        $wizard = new Currency($currencyCode, 0);
        $wizard->setLocale($locale);
        self::assertSame($expectedResult, (string) $wizard);
    }

    public static function providerCurrencyLocaleNoDecimals(): array
    {
        return [
            ["[\$€-fy-NL]\u{a0}#,##0;[\$€-fy-NL]\u{a0}#,##0-", '€', 'fy-NL'], // Trailing negative
            ["[\$€-nl-NL]\u{a0}#,##0;[\$€-nl-NL]\u{a0}-#,##0", '€', 'nl-NL'], // Sign between currency and value
            ["[\$€-nl-BE]\u{a0}#,##0;[\$€-nl-BE]\u{a0}-#,##0", '€', 'NL-BE'], // Sign between currency and value
            ["#,##0\u{a0}[\$€-fr-BE]", '€', 'fr-be'],   // Trailing currency code
            ["#,##0\u{a0}[\$€-el-GR]", '€', 'el-gr'],   // Trailing currency code
            ['[$$-en-CA]#,##0', '$', 'en-ca'],
            ["#,##0\u{a0}[\$\$-fr-CA]", '$', 'fr-ca'],   // Trailing currency code
            ['[$¥-ja-JP]#,##0', '¥', 'ja-JP'], // No decimals to truncate
            ["#,##0\u{a0}[\$د.ب‎-ar-BH]", 'د.ب‎', 'ar-BH'],  // 3 decimals truncated to none
        ];
    }

    public function testCurrencyLocaleInvalidFormat(): void
    {
        if (class_exists(NumberFormatter::class) === false) {
            self::markTestSkipped('Intl extension is not available');
        }

        $locale = 'en-usa';

        $this->expectException(Exception::class);
        $this->expectExceptionMessage("Invalid locale code '{$locale}'");

        $wizard = new Currency('€');
        $wizard->setLocale($locale);
    }

    public function testCurrencyLocaleInvalidCode(): void
    {
        if (class_exists(NumberFormatter::class) === false) {
            self::markTestSkipped('Intl extension is not available');
        }

        $locale = 'nl-GB';

        $this->expectException(Exception::class);
        $this->expectExceptionMessage("Unable to read locale data for '{$locale}'");

        $wizard = new Currency('€');
        $wizard->setLocale($locale);
    }
}
