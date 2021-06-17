<?php

namespace PhpOffice\PhpSpreadsheetTests\Calculation\Functions\Financial;

use PhpOffice\PhpSpreadsheet\Calculation\Financial\Helpers;
use PHPUnit\Framework\TestCase;

class HelpersTest extends TestCase
{
    /**
     * @dataProvider providerDaysPerYear
     *
     * @param mixed $expectedResult
     * @param mixed $year
     * @param mixed $basis
     */
    public function testDaysPerYear($expectedResult, $year, $basis): void
    {
        $result = Helpers::daysPerYear($year, $basis);
        self::assertSame($expectedResult, $result);
    }

    public function providerDaysPerYear(): array
    {
        return require 'tests/data/Calculation/Financial/DaysPerYear.php';
    }
}
