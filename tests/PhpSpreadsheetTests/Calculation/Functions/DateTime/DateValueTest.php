<?php

namespace PhpOffice\PhpSpreadsheetTests\Calculation\Functions\DateTime;

use DateTimeInterface;
use PhpOffice\PhpSpreadsheet\Calculation\DateTime;
use PhpOffice\PhpSpreadsheet\Calculation\Functions;
use PhpOffice\PhpSpreadsheet\Shared\Date;
use PHPUnit\Framework\TestCase;

class DateValueTest extends TestCase
{
    public function setUp()
    {
        Functions::setCompatibilityMode(Functions::COMPATIBILITY_EXCEL);
        Functions::setReturnDateType(Functions::RETURNDATE_EXCEL);
        Date::setExcelCalendar(Date::CALENDAR_WINDOWS_1900);
    }

    /**
     * @dataProvider providerDATEVALUE
     *
     * @param mixed $expectedResult
     * @param $dateValue
     */
    public function testDATEVALUE($expectedResult, $dateValue)
    {
        $result = DateTime::DATEVALUE($dateValue);
        $this->assertEquals($expectedResult, $result, '', 1E-8);
    }

    public function providerDATEVALUE()
    {
        return require 'data/Calculation/DateTime/DATEVALUE.php';
    }

    public function testDATEVALUEtoUnixTimestamp()
    {
        Functions::setReturnDateType(Functions::RETURNDATE_UNIX_TIMESTAMP);

        $result = DateTime::DATEVALUE('2012-1-31');
        $this->assertEquals(1327968000, $result, '', 1E-8);
    }

    public function testDATEVALUEtoDateTimeObject()
    {
        Functions::setReturnDateType(Functions::RETURNDATE_PHP_DATETIME_OBJECT);

        $result = DateTime::DATEVALUE('2012-1-31');
        //    Must return an object...
        $this->assertIsObject($result);
        //    ... of the correct type
        $this->assertTrue(is_a($result, DateTimeInterface::class));
        //    ... with the correct value
        $this->assertEquals($result->format('d-M-Y'), '31-Jan-2012');
    }
}
