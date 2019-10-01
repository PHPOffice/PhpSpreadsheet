<?php

namespace PhpOffice\PhpSpreadsheetTests\Calculation\Functions\DateTime;

use PhpOffice\PhpSpreadsheet\Calculation\DateTime;
use PhpOffice\PhpSpreadsheet\Calculation\Functions;
use PhpOffice\PhpSpreadsheet\Shared\Date;
use PHPUnit\Framework\TestCase;

class Days360Test extends TestCase
{
    public function setUp()
    {
        Functions::setCompatibilityMode(Functions::COMPATIBILITY_EXCEL);
        Functions::setReturnDateType(Functions::RETURNDATE_EXCEL);
        Date::setExcelCalendar(Date::CALENDAR_WINDOWS_1900);
    }

    /**
     * @dataProvider providerDAYS360
     *
     * @param mixed $expectedResult
     * @param $startDate
     * @param $endDate
     * @param $method
     */
    public function testDAYS360($expectedResult, $startDate, $endDate, $method)
    {
        $result = DateTime::DAYS360($startDate, $endDate, $method);
        $this->assertEquals($expectedResult, $result, '', 1E-8);
    }

    public function providerDAYS360()
    {
        return require 'data/Calculation/DateTime/DAYS360.php';
    }
}
