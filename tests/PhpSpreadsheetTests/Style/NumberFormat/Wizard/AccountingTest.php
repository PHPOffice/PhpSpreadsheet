<?php

namespace PhpOffice\PhpSpreadsheetTests\Style\NumberFormat\Wizard;

use NumberFormatter;
use PhpOffice\PhpSpreadsheet\Exception;
use PhpOffice\PhpSpreadsheet\Style\NumberFormat\Wizard\Accounting;
use PhpOffice\PhpSpreadsheet\Style\NumberFormat\Wizard\Currency;
use PhpOffice\PhpSpreadsheet\Style\NumberFormat\Wizard\Number;
use PHPUnit\Framework\TestCase;

class AccountingTest extends TestCase
{
    /**
     * @dataProvider providerAccounting
     */
    public function testAccounting(
        string $expectedResult,
        string $currencyCode,
        int $decimals,
        bool $thousandsSeparator,
        bool $currencySymbolPosition,
        bool $currencySymbolSpacing
    ): void {
        $wizard = new Accounting($currencyCode, $decimals, $thousandsSeparator, $currencySymbolPosition, $currencySymbolSpacing);
        self::assertSame($expectedResult, (string) $wizard);
    }

    public static function providerAccounting(): array
    {
        return [
            ["_-$*\u{a0}0_-", '$', 0, Number::WITHOUT_THOUSANDS_SEPARATOR, Currency::LEADING_SYMBOL, Currency::SYMBOL_WITH_SPACING],
            ["_-$*\u{a0}#,##0_-", '$', 0, Number::WITH_THOUSANDS_SEPARATOR, Currency::LEADING_SYMBOL, Currency::SYMBOL_WITH_SPACING],
            ['_-$*#,##0_-', '$', 0, Number::WITH_THOUSANDS_SEPARATOR, Currency::LEADING_SYMBOL, Currency::SYMBOL_WITHOUT_SPACING],
            ["_-0.00\u{a0}€*_-", '€', 2, Number::WITHOUT_THOUSANDS_SEPARATOR, Currency::TRAILING_SYMBOL, Currency::SYMBOL_WITH_SPACING],
            ["_-#,##0.00\u{a0}€*_-", '€', 2, Number::WITH_THOUSANDS_SEPARATOR, Currency::TRAILING_SYMBOL, Currency::SYMBOL_WITH_SPACING],
            ['_-0.00€*_-', '€', 2, Number::WITHOUT_THOUSANDS_SEPARATOR, Currency::TRAILING_SYMBOL, Currency::SYMBOL_WITHOUT_SPACING],
        ];
    }

    /**
     * @dataProvider providerAccountingLocale
     */
    public function testAccountingLocale(
        string $expectedResult,
        string $currencyCode,
        string $locale
    ): void {
        if (class_exists(NumberFormatter::class) === false) {
            self::markTestSkipped('Intl extension is not available');
        }

        $wizard = new Accounting($currencyCode);
        $wizard->setLocale($locale);
        self::assertSame($expectedResult, (string) $wizard);
    }

    public static function providerAccountingLocale(): array
    {
        return [
            ["[\$€-fy-NL]\u{a0}#,##0.00;([\$€-fy-NL]\u{a0}#,##0.00)", '€', 'fy-NL'],
            ["[\$€-nl-NL]\u{a0}#,##0.00;([\$€-nl-NL]\u{a0}#,##0.00)", '€', 'nl-NL'],
            ["[\$€-nl-BE]\u{a0}#,##0.00;([\$€-nl-BE]\u{a0}#,##0.00)", '€', 'NL-BE'],
            ["#,##0.00\u{a0}[\$€-fr-BE];(#,##0.00\u{a0}[\$€-fr-BE])", '€', 'fr-be'],
            ["#,##0.00\u{a0}[\$€-el-GR]", '€', 'el-gr'],
            ['[$$-en-CA]#,##0.00;([$$-en-CA]#,##0.00)', '$', 'en-ca'],
            ["#,##0.00\u{a0}[\$\$-fr-CA];(#,##0.00\u{a0}[\$\$-fr-CA])", '$', 'fr-ca'],
            ['[$¥-ja-JP]#,##0;([$¥-ja-JP]#,##0)', '¥', 'ja-JP'], // No decimals
            ["#,##0.000\u{a0}[\$د.ب‎-ar-BH]", 'د.ب‎', 'ar-BH'],  // 3 decimals
        ];
    }

    /**
     * @dataProvider providerAccountingLocaleNoDecimals
     */
    public function testAccountingLocaleNoDecimals(
        string $expectedResult,
        string $currencyCode,
        string $locale
    ): void {
        if (class_exists(NumberFormatter::class) === false) {
            self::markTestSkipped('Intl extension is not available');
        }

        $wizard = new Accounting($currencyCode, 0);
        $wizard->setLocale($locale);
        self::assertSame($expectedResult, (string) $wizard);
    }

    public static function providerAccountingLocaleNoDecimals(): array
    {
        return [
            ["[\$€-fy-NL]\u{a0}#,##0;([\$€-fy-NL]\u{a0}#,##0)", '€', 'fy-NL'],
            ["[\$€-nl-NL]\u{a0}#,##0;([\$€-nl-NL]\u{a0}#,##0)", '€', 'nl-NL'],
            ["[\$€-nl-BE]\u{a0}#,##0;([\$€-nl-BE]\u{a0}#,##0)", '€', 'NL-BE'],
            ["#,##0\u{a0}[\$€-fr-BE];(#,##0\u{a0}[\$€-fr-BE])", '€', 'fr-be'],
            ["#,##0\u{a0}[\$€-el-GR]", '€', 'el-gr'],
            ['[$$-en-CA]#,##0;([$$-en-CA]#,##0)', '$', 'en-ca'],
            ["#,##0\u{a0}[\$\$-fr-CA];(#,##0\u{a0}[\$\$-fr-CA])", '$', 'fr-ca'],
            ['[$¥-ja-JP]#,##0;([$¥-ja-JP]#,##0)', '¥', 'ja-JP'], // No decimals to truncate
            ["#,##0\u{a0}[\$د.ب‎-ar-BH]", 'د.ب‎', 'ar-BH'],  // 3 decimals truncated to none
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
