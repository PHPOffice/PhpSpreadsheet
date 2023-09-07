<?php

namespace PhpOffice\PhpSpreadsheetTests\Calculation\Functions\Financial;

use PhpOffice\PhpSpreadsheet\Calculation\Financial\Helpers;
use PHPUnit\Framework\TestCase;

class HelpersTest extends TestCase
{
    /**
     * @dataProvider providerDaysPerYear
     */
    public function testDaysPerYear(mixed $expectedResult, mixed $year, mixed $basis): void
    {
        $result = Helpers::daysPerYear($year, $basis);
        self::assertSame($expectedResult, $result);
    }

    public static function providerDaysPerYear(): array
    {
        return require 'tests/data/Calculation/Financial/DaysPerYear.php';
    }
}
