<?php

namespace PhpOffice\PhpSpreadsheetTests\Calculation\Functions\DateTime;

use PhpOffice\PhpSpreadsheet\Calculation\DateTime;
use PhpOffice\PhpSpreadsheet\Calculation\Functions;
use PhpOffice\PhpSpreadsheet\Shared\Date;
use PHPUnit\Framework\TestCase;

class DaysTest extends TestCase
{
    public function setUp()
    {
        Functions::setCompatibilityMode(Functions::COMPATIBILITY_EXCEL);
        Functions::setReturnDateType(Functions::RETURNDATE_EXCEL);
        Date::setExcelCalendar(Date::CALENDAR_WINDOWS_1900);
    }

    /**
     * @dataProvider providerDAYS
     *
     * @param mixed $expectedResult
     * @param $endDate
     * @param $startDate
     */
    public function testDAYS($expectedResult, $endDate, $startDate)
    {
        $result = DateTime::DAYS($endDate, $startDate);
        $this->assertEquals($expectedResult, $result, '', 1E-8);
    }

    public function providerDAYS()
    {
        return require 'data/Calculation/DateTime/DAYS.php';
    }
}
