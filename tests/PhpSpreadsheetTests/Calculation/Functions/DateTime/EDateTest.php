<?php

namespace PhpOffice\PhpSpreadsheetTests\Calculation\Functions\DateTime;

use PhpOffice\PhpSpreadsheet\Calculation\DateTime;
use PhpOffice\PhpSpreadsheet\Calculation\Functions;
use PhpOffice\PhpSpreadsheet\Shared\Date;
use PHPUnit\Framework\TestCase;

class EDateTest extends TestCase
{
    public function setUp()
    {
        Functions::setCompatibilityMode(Functions::COMPATIBILITY_EXCEL);
        Functions::setReturnDateType(Functions::RETURNDATE_EXCEL);
        Date::setExcelCalendar(Date::CALENDAR_WINDOWS_1900);
    }

    /**
     * @dataProvider providerEDATE
     *
     * @param mixed $expectedResult
     * @param $dateValue
     * @param $adjustmentMonths
     */
    public function testEDATE($expectedResult, $dateValue, $adjustmentMonths)
    {
        $result = DateTime::EDATE($dateValue, $adjustmentMonths);
        $this->assertEquals($expectedResult, $result, '', 1E-8);
    }

    public function providerEDATE()
    {
        return require 'data/Calculation/DateTime/EDATE.php';
    }

    public function testEDATEtoUnixTimestamp()
    {
        Functions::setReturnDateType(Functions::RETURNDATE_UNIX_TIMESTAMP);

        $result = DateTime::EDATE('2012-1-26', -1);
        $this->assertEquals(1324857600, $result, '', 1E-8);
    }

    public function testEDATEtoDateTimeObject()
    {
        Functions::setReturnDateType(Functions::RETURNDATE_PHP_DATETIME_OBJECT);

        $result = DateTime::EDATE('2012-1-26', -1);
        //    Must return an object...
        $this->assertIsObject($result);
        //    ... of the correct type
        $this->assertTrue(is_a($result, 'DateTimeInterface'));
        //    ... with the correct value
        $this->assertEquals($result->format('d-M-Y'), '26-Dec-2011');
    }
}
