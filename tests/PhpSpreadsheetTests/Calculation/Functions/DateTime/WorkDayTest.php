<?php

namespace PhpOffice\PhpSpreadsheetTests\Calculation\Functions\DateTime;

use PhpOffice\PhpSpreadsheet\Calculation\DateTime;
use PhpOffice\PhpSpreadsheet\Calculation\Functions;
use PhpOffice\PhpSpreadsheet\Shared\Date;
use PHPUnit\Framework\TestCase;

class WorkDayTest extends TestCase
{
    public function setUp()
    {
        Functions::setCompatibilityMode(Functions::COMPATIBILITY_EXCEL);
        Functions::setReturnDateType(Functions::RETURNDATE_EXCEL);
        Date::setExcelCalendar(Date::CALENDAR_WINDOWS_1900);
    }

    /**
     * @dataProvider providerWORKDAY
     *
     * @param mixed $expectedResult
     */
    public function testWORKDAY($expectedResult, ...$args)
    {
        $result = DateTime::WORKDAY(...$args);
        $this->assertEquals($expectedResult, $result, '', 1E-8);
    }

    public function providerWORKDAY()
    {
        return require 'data/Calculation/DateTime/WORKDAY.php';
    }
}
