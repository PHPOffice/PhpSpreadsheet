<?php

namespace PhpOffice\PhpSpreadsheetTests\Style;

use PhpOffice\PhpSpreadsheet\Shared\StringHelper;
use PhpOffice\PhpSpreadsheet\Style\NumberFormat;
use PHPUnit\Framework\TestCase;

class NumberFormatTest extends TestCase
{
    protected function setUp(): void
    {
        StringHelper::setDecimalSeparator('.');
        StringHelper::setThousandsSeparator(',');
    }

    /**
     * @dataProvider providerNumberFormat
     *
     * @param mixed $expectedResult
     */
    public function testFormatValueWithMask($expectedResult, ...$args): void
    {
        $result = NumberFormat::toFormattedString(...$args);
        self::assertEquals($expectedResult, $result);
    }

    public function providerNumberFormat()
    {
        return require 'tests/data/Style/NumberFormat.php';
    }

    /**
     * @dataProvider providerNumberFormatDates
     *
     * @param mixed $expectedResult
     */
    public function testFormatValueWithMaskDate($expectedResult, ...$args): void
    {
        $result = NumberFormat::toFormattedString(...$args);
        self::assertEquals($expectedResult, $result);
    }

    public function providerNumberFormatDates()
    {
        return require 'tests/data/Style/NumberFormatDates.php';
    }

    public function testCurrencyCode(): void
    {
        // "Currency symbol" replaces $ in some cases, not in others
        $cur = StringHelper::getCurrencyCode();
        StringHelper::setCurrencyCode('€');
        $fmt1 = '#,##0.000\ [$]';
        $rslt = NumberFormat::toFormattedString(12345.679, $fmt1);
        self::assertEquals($rslt, '12,345.679 €');
        $fmt2 = '$ #,##0.000';
        $rslt = NumberFormat::toFormattedString(12345.679, $fmt2);
        self::assertEquals($rslt, '$ 12,345.679');
        StringHelper::setCurrencyCode($cur);
    }
}
