<?php

namespace PhpOffice\PhpSpreadsheetTests\Calculation\Functions\DateTime;

use PhpOffice\PhpSpreadsheet\Calculation\DateTime;
use PhpOffice\PhpSpreadsheet\Calculation\Functions;
use PhpOffice\PhpSpreadsheet\Shared\Date;
use PHPUnit\Framework\TestCase;

class DateDifTest extends TestCase
{
    protected function setUp(): void
    {
        Functions::setCompatibilityMode(Functions::COMPATIBILITY_EXCEL);
        Functions::setReturnDateType(Functions::RETURNDATE_EXCEL);
        Date::setExcelCalendar(Date::CALENDAR_WINDOWS_1900);
    }

    /**
     * @dataProvider providerDATEDIF
     *
     * @param mixed $expectedResult
     * @param $startDate
     * @param $endDate
     * @param $unit
     */
    public function testDATEDIF($expectedResult, $startDate, $endDate, $unit): void
    {
        $result = DateTime::DATEDIF($startDate, $endDate, $unit);
        self::assertEqualsWithDelta($expectedResult, $result, 1E-8);
    }

    public function providerDATEDIF()
    {
        return require 'tests/data/Calculation/DateTime/DATEDIF.php';
    }
}
