<?php

namespace PhpOffice\PhpSpreadsheetTests\Calculation\Functions\DateTime;

use PhpOffice\PhpSpreadsheet\Calculation\DateTime;
use PhpOffice\PhpSpreadsheet\Calculation\Functions;
use PhpOffice\PhpSpreadsheet\Shared\Date;
use PHPUnit\Framework\TestCase;

class TimeTest extends TestCase
{
    public function setUp()
    {
        Functions::setCompatibilityMode(Functions::COMPATIBILITY_EXCEL);
        Functions::setReturnDateType(Functions::RETURNDATE_EXCEL);
        Date::setExcelCalendar(Date::CALENDAR_WINDOWS_1900);
    }

    /**
     * @dataProvider providerTIME
     *
     * @param mixed $expectedResult
     */
    public function testTIME($expectedResult, ...$args)
    {
        $result = DateTime::TIME(...$args);
        $this->assertEquals($expectedResult, $result, '', 1E-8);
    }

    public function providerTIME()
    {
        return require 'data/Calculation/DateTime/TIME.php';
    }

    public function testTIMEtoUnixTimestamp()
    {
        Functions::setReturnDateType(Functions::RETURNDATE_PHP_NUMERIC);

        $result = DateTime::TIME(7, 30, 20);
        $this->assertEquals(27020, $result, '', 1E-8);
    }

    public function testTIMEtoDateTimeObject()
    {
        Functions::setReturnDateType(Functions::RETURNDATE_PHP_OBJECT);

        $result = DateTime::TIME(7, 30, 20);
        //    Must return an object...
        $this->assertIsObject($result);
        //    ... of the correct type
        $this->assertTrue(is_a($result, 'DateTimeInterface'));
        //    ... with the correct value
        $this->assertEquals($result->format('H:i:s'), '07:30:20');
    }
}
