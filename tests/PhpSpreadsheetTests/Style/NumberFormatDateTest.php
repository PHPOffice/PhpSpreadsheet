<?php

namespace PhpOffice\PhpSpreadsheetTests\Style;

use PhpOffice\PhpSpreadsheet\Shared\StringHelper;
use PhpOffice\PhpSpreadsheet\Style\NumberFormat;

class NumberFormatDateTest extends \PHPUnit_Framework_TestCase
{
    public function setUp()
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
        $this->assertEquals($expectedResult, $result);
    }

    public function providerNumberFormat()
    {
        return require 'data/Style/NumberFormatDates.php';
    }
}
