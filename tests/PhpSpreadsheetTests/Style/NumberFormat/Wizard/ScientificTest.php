<?php

declare(strict_types=1);

namespace PhpOffice\PhpSpreadsheetTests\Style\NumberFormat\Wizard;

use NumberFormatter;
use PhpOffice\PhpSpreadsheet\Exception;
use PhpOffice\PhpSpreadsheet\Style\NumberFormat\Wizard\Scientific;
use PHPUnit\Framework\TestCase;

class ScientificTest extends TestCase
{
    /**
     * @dataProvider providerScientific
     */
    public function testScientific(string $expectedResult, int $decimals): void
    {
        $wizard = new Scientific($decimals);
        self::assertSame($expectedResult, (string) $wizard);
    }

    public static function providerScientific(): array
    {
        return [
            ['0E+00', 0],
            ['0.0E+00', 1],
            ['0.00E+00', 2],
            ['0.000E+00', 3],
            ['0E+00', -1],
            ['0.000000000000000000000000000000E+00', 31],
        ];
    }

    /**
     * @dataProvider providerScientificLocale
     */
    public function testScientificLocale(
        string $expectedResult,
        string $locale
    ): void {
        if (class_exists(NumberFormatter::class) === false) {
            self::markTestSkipped('Intl extension is not available');
        }

        $wizard = new Scientific(2);
        $wizard->setLocale($locale);
        self::assertSame($expectedResult, (string) $wizard);
    }

    public static function providerScientificLocale(): array
    {
        return [
            ['0.00E+00', 'en'],
            ['0.00E+00', 'az-AZ'],
            ['0.00E+00', 'az-Cyrl'],
            ['0.00E+00', 'az-Cyrl-AZ'],
            ['0.00E+00', 'az-Latn'],
            ['0.00E+00', 'az-Latn-AZ'],
        ];
    }

    public function testScientificLocaleInvalidFormat(): void
    {
        if (class_exists(NumberFormatter::class) === false) {
            self::markTestSkipped('Intl extension is not available');
        }

        $locale = 'en-usa';

        $this->expectException(Exception::class);
        $this->expectExceptionMessage("Invalid locale code '{$locale}'");

        $wizard = new Scientific(2);
        $wizard->setLocale($locale);
    }
}
