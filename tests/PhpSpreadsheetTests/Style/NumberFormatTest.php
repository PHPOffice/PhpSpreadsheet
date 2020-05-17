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
    public function testFormatValueWithMask($expectedResult, ...$args)
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
    public function testFormatValueWithMaskDate($expectedResult, ...$args)
    {
        $result = NumberFormat::toFormattedString(...$args);
        self::assertEquals($expectedResult, $result);
    }

    public function providerNumberFormatDates()
    {
        return require 'tests/data/Style/NumberFormatDates.php';
    }
}
