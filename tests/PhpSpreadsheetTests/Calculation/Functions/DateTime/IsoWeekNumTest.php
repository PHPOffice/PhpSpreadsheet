<?php

namespace PhpOffice\PhpSpreadsheetTests\Calculation\Functions\DateTime;

use PhpOffice\PhpSpreadsheet\Calculation\DateTime;
use PhpOffice\PhpSpreadsheet\Calculation\Functions;
use PhpOffice\PhpSpreadsheet\Shared\Date;
use PHPUnit\Framework\TestCase;

class IsoWeekNumTest extends TestCase
{
    public function setUp()
    {
        Functions::setCompatibilityMode(Functions::COMPATIBILITY_EXCEL);
        Functions::setReturnDateType(Functions::RETURNDATE_EXCEL);
        Date::setExcelCalendar(Date::CALENDAR_WINDOWS_1900);
    }

    /**
     * @dataProvider providerISOWEEKNUM
     *
     * @param mixed $expectedResult
     * @param mixed $dateValue
     */
    public function testISOWEEKNUM($expectedResult, $dateValue)
    {
        $result = DateTime::ISOWEEKNUM($dateValue);
        $this->assertEquals($expectedResult, $result, '', 1E-8);
    }

    public function providerISOWEEKNUM()
    {
        return require 'data/Calculation/DateTime/ISOWEEKNUM.php';
    }
}
