<?php

namespace PhpOffice\PhpSpreadsheetTests\Calculation\Functions\DateTime;

use PhpOffice\PhpSpreadsheet\Calculation\DateTime;
use PhpOffice\PhpSpreadsheet\Calculation\Functions;
use PhpOffice\PhpSpreadsheet\Shared\Date;
use PHPUnit\Framework\TestCase;

class YearFracTest extends TestCase
{
    public function setUp()
    {
        Functions::setCompatibilityMode(Functions::COMPATIBILITY_EXCEL);
        Functions::setReturnDateType(Functions::RETURNDATE_EXCEL);
        Date::setExcelCalendar(Date::CALENDAR_WINDOWS_1900);
    }

    /**
     * @dataProvider providerYEARFRAC
     *
     * @param mixed $expectedResult
     */
    public function testYEARFRAC($expectedResult, ...$args)
    {
        $result = DateTime::YEARFRAC(...$args);
        $this->assertEquals($expectedResult, $result, '', 1E-8);
    }

    public function providerYEARFRAC()
    {
        return require 'data/Calculation/DateTime/YEARFRAC.php';
    }
}
