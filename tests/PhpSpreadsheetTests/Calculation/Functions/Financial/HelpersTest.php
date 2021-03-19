<?php

namespace PhpOffice\PhpSpreadsheetTests\Calculation\Functions\Financial;

use PhpOffice\PhpSpreadsheet\Calculation\Financial\Helpers;
use PhpOffice\PhpSpreadsheet\Calculation\Functions;
use PHPUnit\Framework\TestCase;

class HelpersTest extends TestCase
{
    /**
     * @dataProvider providerDaysPerYear
     *
     * @param mixed $expectedResult
     * @param mixed $year
     */
    public function testDaysPerYear($expectedResult, $year, $basis)
    {
        $result = Helpers::daysPerYear($year, $basis);
        self::assertSame($expectedResult, $result, 1E-8);
    }

    public function providerDaysPerYear()
    {
        return require 'tests/data/Calculation/Financial/DaysPerYear.php';
    }
}
