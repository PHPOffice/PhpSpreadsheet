<?php

namespace PhpOffice\PhpSpreadsheetTests\Calculation\Functions\DateTime;

use PhpOffice\PhpSpreadsheet\Calculation\DateTimeExcel\YearFrac;

class YearFracTest extends AllSetupTeardown
{
    /**
     * @dataProvider providerYEARFRAC
     *
     * @param mixed $expectedResult
     */
    public function testYEARFRAC($expectedResult, ...$args): void
    {
        $result = YearFrac::funcYearfrac(...$args);
        self::assertEqualsWithDelta($expectedResult, $result, 1E-8);
    }

    public function providerYEARFRAC()
    {
        return require 'tests/data/Calculation/DateTime/YEARFRAC.php';
    }
}
