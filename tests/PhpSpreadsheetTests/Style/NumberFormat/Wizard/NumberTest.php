<?php

declare(strict_types=1);

namespace PhpOffice\PhpSpreadsheetTests\Style\NumberFormat\Wizard;

use NumberFormatter;
use PhpOffice\PhpSpreadsheet\Exception;
use PhpOffice\PhpSpreadsheet\Style\NumberFormat\Wizard\Number;
use PHPUnit\Framework\TestCase;

class NumberTest extends TestCase
{
    /**
     * @dataProvider providerNumber
     */
    public function testNumber(string $expectedResult, int $decimals, bool $thousandsSeparator): void
    {
        $wizard = new Number($decimals, $thousandsSeparator);
        self::assertSame($expectedResult, (string) $wizard);
    }

    public static function providerNumber(): array
    {
        return [
            ['0', 0, Number::WITHOUT_THOUSANDS_SEPARATOR],
            ['#,##0', 0, Number::WITH_THOUSANDS_SEPARATOR],
            ['0.0', 1, Number::WITHOUT_THOUSANDS_SEPARATOR],
            ['#,##0.0', 1, Number::WITH_THOUSANDS_SEPARATOR],
            ['0.00', 2, Number::WITHOUT_THOUSANDS_SEPARATOR],
            ['#,##0.00', 2, Number::WITH_THOUSANDS_SEPARATOR],
        ];
    }

    /**
     * @dataProvider providerNumberLocale
     */
    public function testNumberLocale(
        string $expectedResult,
        string $locale
    ): void {
        if (class_exists(NumberFormatter::class) === false) {
            self::markTestSkipped('Intl extension is not available');
        }

        $wizard = new Number(2);
        $wizard->setLocale($locale);
        self::assertSame($expectedResult, (string) $wizard);
    }

    public static function providerNumberLocale(): array
    {
        return [
            ['#,##0.00', 'en-us'],
        ];
    }

    public function testNumberLocaleInvalidFormat(): void
    {
        if (class_exists(NumberFormatter::class) === false) {
            self::markTestSkipped('Intl extension is not available');
        }

        $locale = 'en-usa';

        $this->expectException(Exception::class);
        $this->expectExceptionMessage("Invalid locale code '{$locale}'");

        $wizard = new Number(2);
        $wizard->setLocale($locale);
    }
}
