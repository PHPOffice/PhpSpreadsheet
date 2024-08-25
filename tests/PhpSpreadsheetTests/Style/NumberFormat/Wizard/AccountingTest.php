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

class AccountingTest extends TestCase
{
    /**
     * @dataProvider providerAccounting
     */
    public function testAccounting(
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
        $wizard = new Accounting($currencyCode, $decimals, $thousandsSeparator, $currencySymbolPosition, $currencySymbolSpacing, negative: $negative);
        self::assertSame($expectedResultPositive, Formatter::toFormattedString(1234.56, $wizard->format()));
        self::assertSame($expectedResultNegative, Formatter::toFormattedString(-1234.56, $wizard->format()));
        self::assertSame($expectedResultZero, Formatter::toFormattedString(0, $wizard->format()));
    }

    public static function providerAccounting(): array
    {
        return [
            [' $ 1235 ', ' $ (1235)', ' $ - ', '$', 0, Number::WITHOUT_THOUSANDS_SEPARATOR, Currency::LEADING_SYMBOL, Currency::SYMBOL_WITH_SPACING],
            [' $ 1,235 ', ' $ (1,235)', ' $ - ', '$', 0, Number::WITH_THOUSANDS_SEPARATOR, Currency::LEADING_SYMBOL, Currency::SYMBOL_WITH_SPACING],
            [' $ 1,235 ', ' $ (1,235)', ' $ - ', '$', 0, Number::WITH_THOUSANDS_SEPARATOR, Currency::LEADING_SYMBOL, Currency::SYMBOL_WITHOUT_SPACING],
            [' 1234.56 € ', ' (1234.56)€ ', ' - € ', '€', 2, Number::WITHOUT_THOUSANDS_SEPARATOR, Currency::TRAILING_SYMBOL, Currency::SYMBOL_WITH_SPACING],
            [' 1,234.56 € ', ' (1,234.56)€ ', ' - € ', '€', 2, Number::WITH_THOUSANDS_SEPARATOR, Currency::TRAILING_SYMBOL, Currency::SYMBOL_WITH_SPACING],
            [' 1234.560 € ', ' (1234.560)€ ', ' - € ', '€', 3, Number::WITHOUT_THOUSANDS_SEPARATOR, Currency::TRAILING_SYMBOL, Currency::SYMBOL_WITHOUT_SPACING],
        ];
    }

    /**
     * @dataProvider providerAccountingLocale
     */
    public function testAccountingLocale(
        string $expectedResult,
        string $currencyCode,
        string $locale,
        ?bool $stripRLM = null
    ): void {
        if (class_exists(NumberFormatter::class) === false) {
            self::markTestSkipped('Intl extension is not available');
        }

        $wizard = new Accounting($currencyCode);
        if ($stripRLM !== null) {
            $wizard->setStripLeadingRLM($stripRLM);
        }
        $wizard->setLocale($locale);
        self::assertSame($expectedResult, (string) $wizard);
    }

    public static function providerAccountingLocale(): array
    {
        // \u{a0} is non-breaking space
        // \u{200e} is LRM (left-to-right mark)
        return [
            ["[\$€-fy-NL]\u{a0}#,##0.00;([\$€-fy-NL]\u{a0}#,##0.00)", '€', 'fy-NL'],
            ["[\$€-nl-NL]\u{a0}#,##0.00;([\$€-nl-NL]\u{a0}#,##0.00)", '€', 'nl-NL'],
            ["[\$€-nl-BE]\u{a0}#,##0.00;([\$€-nl-BE]\u{a0}#,##0.00)", '€', 'NL-BE'],
            ["#,##0.00\u{a0}[\$€-fr-BE];(#,##0.00\u{a0}[\$€-fr-BE])", '€', 'fr-be'],
            ["#,##0.00\u{a0}[\$€-el-GR]", '€', 'el-gr'],
            ['[$$-en-CA]#,##0.00;([$$-en-CA]#,##0.00)', '$', 'en-ca'],
            ["#,##0.00\u{a0}[\$\$-fr-CA];(#,##0.00\u{a0}[\$\$-fr-CA])", '$', 'fr-ca'],
            ['[$¥-ja-JP]#,##0;([$¥-ja-JP]#,##0)', '¥', 'ja-JP'], // No decimals
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
        $wizardFalse = new Accounting($currencyCode);
        $wizardFalse->setStripLeadingRLM(false);
        $wizardFalse->setLocale($locale);
        $stringFalse = (string) $wizardFalse;
        $wizardTrue = new Accounting($currencyCode);
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
     * @dataProvider providerAccountingLocaleNoDecimals
     */
    public function testAccountingLocaleNoDecimals(
        string $expectedResult,
        string $currencyCode,
        string $locale,
        ?bool $stripRLM = null
    ): void {
        if (class_exists(NumberFormatter::class) === false) {
            self::markTestSkipped('Intl extension is not available');
        }

        $wizard = new Accounting($currencyCode, 0);
        if ($stripRLM !== null) {
            $wizard->setStripLeadingRLM($stripRLM);
        }
        $wizard->setLocale($locale);
        self::assertSame($expectedResult, (string) $wizard);
    }

    public static function providerAccountingLocaleNoDecimals(): array
    {
        // \u{a0} is non-breaking space
        // \u{200e} is LRM (left-to-right mark)
        return [
            ["[\$€-fy-NL]\u{a0}#,##0;([\$€-fy-NL]\u{a0}#,##0)", '€', 'fy-NL'],
            ["[\$€-nl-NL]\u{a0}#,##0;([\$€-nl-NL]\u{a0}#,##0)", '€', 'nl-NL'],
            ["[\$€-nl-BE]\u{a0}#,##0;([\$€-nl-BE]\u{a0}#,##0)", '€', 'NL-BE'],
            ["#,##0\u{a0}[\$€-fr-BE];(#,##0\u{a0}[\$€-fr-BE])", '€', 'fr-be'],
            ["#,##0\u{a0}[\$€-el-GR]", '€', 'el-gr'],
            ['[$$-en-CA]#,##0;([$$-en-CA]#,##0)', '$', 'en-ca'],
            ["#,##0\u{a0}[\$\$-fr-CA];(#,##0\u{a0}[\$\$-fr-CA])", '$', 'fr-ca'],
            ['[$¥-ja-JP]#,##0;([$¥-ja-JP]#,##0)', '¥', 'ja-JP'], // No decimals to truncate
            ["#,##0\u{a0}[\$د.ب\u{200e}-ar-BH]", "د.ب\u{200e}", 'ar-BH', true],  // 3 decimals truncated to none
        ];
    }

    public function testAccountingLocaleInvalidFormat(): void
    {
        if (class_exists(NumberFormatter::class) === false) {
            self::markTestSkipped('Intl extension is not available');
        }

        $locale = 'en-usa';

        $this->expectException(Exception::class);
        $this->expectExceptionMessage("Invalid locale code '{$locale}'");

        $wizard = new Accounting('€');
        $wizard->setLocale($locale);
    }

    public function testAccountingLocaleInvalidCode(): void
    {
        if (class_exists(NumberFormatter::class) === false) {
            self::markTestSkipped('Intl extension is not available');
        }

        $locale = 'nl-GB';

        $this->expectException(Exception::class);
        $this->expectExceptionMessage("Unable to read locale data for '{$locale}'");

        $wizard = new Accounting('€');
        $wizard->setLocale($locale);
    }
}
