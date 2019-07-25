<?php

namespace PhpOffice\PhpSpreadsheetTests\Calculation\Functions\DateTime;

use PhpOffice\PhpSpreadsheet\Calculation\DateTime;
use PhpOffice\PhpSpreadsheet\Calculation\Functions;
use PhpOffice\PhpSpreadsheet\Shared\Date;
use PHPUnit\Framework\TestCase;

class HourTest extends TestCase
{
    public function setUp()
    {
        Functions::setCompatibilityMode(Functions::COMPATIBILITY_EXCEL);
        Functions::setReturnDateType(Functions::RETURNDATE_EXCEL);
        Date::setExcelCalendar(Date::CALENDAR_WINDOWS_1900);
    }

    /**
     * @dataProvider providerHOUR
     *
     * @param mixed $expectedResult
     * @param $dateTimeValue
     */
    public function testHOUR($expectedResult, $dateTimeValue)
    {
        $result = DateTime::HOUROFDAY($dateTimeValue);
        $this->assertEquals($expectedResult, $result, '', 1E-8);
    }

    public function providerHOUR()
    {
        return require 'data/Calculation/DateTime/HOUR.php';
    }
}
