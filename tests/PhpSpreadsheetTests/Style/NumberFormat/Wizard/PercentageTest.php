<?php

namespace PhpOffice\PhpSpreadsheetTests\Style\NumberFormat\Wizard;

use NumberFormatter;
use PhpOffice\PhpSpreadsheet\Exception;
use PhpOffice\PhpSpreadsheet\Style\NumberFormat\Wizard\Percentage;
use PHPUnit\Framework\TestCase;

class PercentageTest extends TestCase
{
    /**
     * @dataProvider providerPercentage
     */
    public function testPercentage(string $expectedResult, int $decimals): void
    {
        $wizard = new Percentage($decimals);
        self::assertSame($expectedResult, (string) $wizard);
    }

    public static function providerPercentage(): array
    {
        return [
            ['0%', 0],
            ['0.0%', 1],
            ['0.00%', 2],
            ['0.000%', 3],
            ['0%', -1],
            ['0.000000000000000000000000000000%', 31],
        ];
    }

    /**
     * @dataProvider providerPercentageLocale
     */
    public function testPercentageLocale(
        string $expectedResult,
        string $locale
    ): void {
        if (class_exists(NumberFormatter::class) === false) {
            self::markTestSkipped('Intl extension is not available');
        }

        $wizard = new Percentage(2);
        $wizard->setLocale($locale);
        self::assertSame($expectedResult, (string) $wizard);
    }

    public static function providerPercentageLocale(): array
    {
        return [
            ['#,##0.00%', 'fy-NL'],
            ['#,##0.00%', 'nl-NL'],
            ['#,##0.00%', 'NL-BE'],
            ["#,##0.00\u{a0}%", 'fr-be'],
            ['#,##0.00%', 'el-gr'],
            ['#,##0.00%', 'en-ca'],
            ["#,##0.00\u{a0}%", 'fr-ca'],
        ];
    }

    public function testPercentageLocaleInvalidFormat(): void
    {
        if (class_exists(NumberFormatter::class) === false) {
            self::markTestSkipped('Intl extension is not available');
        }

        $locale = 'en-usa';

        $this->expectException(Exception::class);
        $this->expectExceptionMessage("Invalid locale code '{$locale}'");

        $wizard = new Percentage(2);
        $wizard->setLocale($locale);
    }

    public function testPercentageLocaleInvalidCode(): void
    {
        if (class_exists(NumberFormatter::class) === false) {
            self::markTestSkipped('Intl extension is not available');
        }

        $locale = 'nl-GB';

        $this->expectException(Exception::class);
        $this->expectExceptionMessage("Unable to read locale data for '{$locale}'");

        $wizard = new Percentage(2);
        $wizard->setLocale($locale);
    }
}
