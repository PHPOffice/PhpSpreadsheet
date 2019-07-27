<?php

namespace PhpOffice\PhpSpreadsheetTests\Calculation\Functions\DateTime;

use PhpOffice\PhpSpreadsheet\Calculation\DateTime;
use PhpOffice\PhpSpreadsheet\Calculation\Functions;
use PhpOffice\PhpSpreadsheet\Shared\Date;
use PHPUnit\Framework\TestCase;

class TimeValueTest extends TestCase
{
    public function setUp()
    {
        Functions::setCompatibilityMode(Functions::COMPATIBILITY_EXCEL);
        Functions::setReturnDateType(Functions::RETURNDATE_EXCEL);
        Date::setExcelCalendar(Date::CALENDAR_WINDOWS_1900);
    }

    /**
     * @dataProvider providerTIMEVALUE
     *
     * @param mixed $expectedResult
     * @param $timeValue
     */
    public function testTIMEVALUE($expectedResult, $timeValue)
    {
        $result = DateTime::TIMEVALUE($timeValue);
        $this->assertEquals($expectedResult, $result, '', 1E-8);
    }

    public function providerTIMEVALUE()
    {
        return require 'data/Calculation/DateTime/TIMEVALUE.php';
    }

    public function testTIMEVALUEtoUnixTimestamp()
    {
        Functions::setReturnDateType(Functions::RETURNDATE_UNIX_TIMESTAMP);

        $result = DateTime::TIMEVALUE('7:30:20');
        $this->assertEquals(23420, $result, '', 1E-8);
    }

    public function testTIMEVALUEtoDateTimeObject()
    {
        Functions::setReturnDateType(Functions::RETURNDATE_PHP_DATETIME_OBJECT);

        $result = DateTime::TIMEVALUE('7:30:20');
        //    Must return an object...
        $this->assertIsObject($result);
        //    ... of the correct type
        $this->assertTrue(is_a($result, 'DateTimeInterface'));
        //    ... with the correct value
        $this->assertEquals($result->format('H:i:s'), '07:30:20');
    }
}
