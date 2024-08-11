<?php

declare(strict_types=1);

namespace PhpOffice\PhpSpreadsheetTests\Style\NumberFormat\Wizard;

use NumberFormatter;
use PhpOffice\PhpSpreadsheet\Exception;
use PhpOffice\PhpSpreadsheet\Style\NumberFormat\Formatter;
use PhpOffice\PhpSpreadsheet\Style\NumberFormat\Wizard\Accounting;
use PhpOffice\PhpSpreadsheet\Style\NumberFormat\Wizard\Currency;
use PhpOffice\PhpSpreadsheet\Style\NumberFormat\Wizard\CurrencyNegative;
use PhpOffice\PhpSpreadsheet\Style\NumberFormat\Wizard\Number;
use PHPUnit\Framework\TestCase;

class CurrencyTest extends TestCase
{
    /**
     * @dataProvider providerCurrency
     */
    public function testCurrency(
        string $expectedResultPositive,
        string $expectedResultNegative,
        string $expectedResultZero,
        string $currencyCode,
        int $decimals,
        bool $thousandsSeparator,
        bool $currencySymbolPosition,
        bool $currencySymbolSpacing,
        CurrencyNegative $negative = CurrencyNegative::minus
    ): void {
        $wizard = new Currency($currencyCode, $decimals, $thousandsSeparator, $currencySymbolPosition, $currencySymbolSpacing, negative: $negative);
        self::assertSame($expectedResultPositive, Formatter::toFormattedString(1234.56, $wizard->format()));
        self::assertSame($expectedResultNegative, Formatter::toFormattedString(-1234.56, $wizard->format()));
        self::assertSame($expectedResultZero, Formatter::toFormattedString(0, $wizard->format()));
    }

    public static function providerCurrency(): array
    {
        return [
            [' $1235 ', ' -$1235', ' $0 ', '$', 0, Number::WITHOUT_THOUSANDS_SEPARATOR, Currency::LEADING_SYMBOL, Currency::SYMBOL_WITH_SPACING],
            [' $1,235 ', ' -$1,235', ' $0 ', '$', 0, Number::WITH_THOUSANDS_SEPARATOR, Currency::LEADING_SYMBOL, Currency::SYMBOL_WITH_SPACING],
            [' $1,235 ', ' -$1,235', ' $0 ', '$', 0, Number::WITH_THOUSANDS_SEPARATOR, Currency::LEADING_SYMBOL, Currency::SYMBOL_WITHOUT_SPACING],
            [' 1234.56€ ', ' -1234.56€ ', ' 0.00€ ', '€', 2, Number::WITHOUT_THOUSANDS_SEPARATOR, Currency::TRAILING_SYMBOL, Currency::SYMBOL_WITH_SPACING],
            [' 1,234.56€ ', ' -1,234.56€ ', ' 0.00€ ', '€', 2, Number::WITH_THOUSANDS_SEPARATOR, Currency::TRAILING_SYMBOL, Currency::SYMBOL_WITH_SPACING],
            [' 1234.56€ ', ' -1234.56€ ', ' 0.00€ ', '€', 2, Number::WITHOUT_THOUSANDS_SEPARATOR, Currency::TRAILING_SYMBOL, Currency::SYMBOL_WITHOUT_SPACING],
            [' 1234.56€ ', ' (1234.56)€ ', ' 0.00€ ', '€', 2, Number::WITHOUT_THOUSANDS_SEPARATOR, Currency::TRAILING_SYMBOL, Currency::SYMBOL_WITHOUT_SPACING, CurrencyNegative::parentheses],
        ];
    }

    /**
     * @dataProvider providerCurrencyLocale
     */
    public function testCurrencyLocale(
        string $expectedResult,
        string $currencyCode,
        string $locale,
        ?bool $stripRLM = null
    ): void {
        if (class_exists(NumberFormatter::class) === false) {
            self::markTestSkipped('Intl extension is not available');
        }

        $wizard = new Currency($currencyCode);
        if ($stripRLM !== null) {
            $wizard->setStripLeadingRLM($stripRLM);
        }
        $wizard->setLocale($locale);
        self::assertSame($expectedResult, (string) $wizard);
    }

    public static function providerCurrencyLocale(): array
    {
        // \u{a0} is non-breaking space
        // \u{200e} is LRM (left-to-right mark)
        return [
            ["[\$€-fy-NL]\u{a0}#,##0.00;[\$€-fy-NL]\u{a0}#,##0.00-", '€', 'fy-NL'], // Trailing negative
            ["[\$€-nl-NL]\u{a0}#,##0.00;[\$€-nl-NL]\u{a0}-#,##0.00", '€', 'nl-NL'], // Sign between currency and value
            ["[\$€-nl-BE]\u{a0}#,##0.00;[\$€-nl-BE]\u{a0}-#,##0.00", '€', 'NL-BE'], // Sign between currency and value
            ["#,##0.00\u{a0}[\$€-fr-BE]", '€', 'fr-be'],   // Trailing currency code
            ["#,##0.00\u{a0}[\$€-el-GR]", '€', 'el-gr'],   // Trailing currency code
            ['[$$-en-CA]#,##0.00', '$', 'en-ca'],
            ["#,##0.00\u{a0}[\$\$-fr-CA]", '$', 'fr-ca'],   // Trailing currency code
            ['[$¥-ja-JP]#,##0', '¥', 'ja-JP'], // No decimals
            ["#,##0.000\u{a0}[\$د.ب\u{200e}-ar-BH]", "د.ب\u{200e}", 'ar-BH', true],  // 3 decimals
        ];
    }

    public function testIcu721(): void
    {
        if (class_exists(NumberFormatter::class) === false) {
            self::markTestSkipped('Intl extension is not available');
        }

        $currencyCode = "د.ب\u{200e}";
        $locale = 'ar-BH';
        $wizardFalse = new Currency($currencyCode);
        $wizardFalse->setStripLeadingRLM(false);
        $wizardFalse->setLocale($locale);
        $stringFalse = (string) $wizardFalse;
        $wizardTrue = new Currency($currencyCode);
        $wizardTrue->setStripLeadingRLM(true);
        $wizardTrue->setLocale($locale);
        $stringTrue = (string) $wizardTrue;
        $version = Accounting::icuVersion();
        if ($version < 72.1) {
            self::assertSame($stringFalse, $stringTrue);
        } else {
            self::assertSame("\u{200f}$stringTrue", $stringFalse);
        }
    }

    /**
     * @dataProvider providerCurrencyLocaleNoDecimals
     */
    public function testCurrencyLocaleNoDecimals(
        string $expectedResult,
        string $currencyCode,
        string $locale,
        ?bool $stripRLM = null
    ): void {
        if (class_exists(NumberFormatter::class) === false) {
            self::markTestSkipped('Intl extension is not available');
        }

        $wizard = new Currency($currencyCode, 0);
        if ($stripRLM !== null) {
            $wizard->setStripLeadingRLM($stripRLM);
        }
        $wizard->setLocale($locale);
        self::assertSame($expectedResult, (string) $wizard);
    }

    public static function providerCurrencyLocaleNoDecimals(): array
    {
        // \u{a0} is non-breaking space
        // \u{200e} is LRM (left-to-right mark)
        return [
            ["[\$€-fy-NL]\u{a0}#,##0;[\$€-fy-NL]\u{a0}#,##0-", '€', 'fy-NL'], // Trailing negative
            ["[\$€-nl-NL]\u{a0}#,##0;[\$€-nl-NL]\u{a0}-#,##0", '€', 'nl-NL'], // Sign between currency and value
            ["[\$€-nl-BE]\u{a0}#,##0;[\$€-nl-BE]\u{a0}-#,##0", '€', 'NL-BE'], // Sign between currency and value
            ["#,##0\u{a0}[\$€-fr-BE]", '€', 'fr-be'],   // Trailing currency code
            ["#,##0\u{a0}[\$€-el-GR]", '€', 'el-gr'],   // Trailing currency code
            ['[$$-en-CA]#,##0', '$', 'en-ca'],
            ["#,##0\u{a0}[\$\$-fr-CA]", '$', 'fr-ca'],   // Trailing currency code
            ['[$¥-ja-JP]#,##0', '¥', 'ja-JP'], // No decimals to truncate
            ["#,##0\u{a0}[\$د.ب\u{200e}-ar-BH]", "د.ب\u{200e}", 'ar-BH', true],  // 3 decimals truncated to none
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
