<?php

namespace PhpOffice\PhpSpreadsheetTests\Calculation\Functions\DateTime;

use PhpOffice\PhpSpreadsheet\Calculation\DateTime;
use PhpOffice\PhpSpreadsheet\Calculation\Functions;
use PhpOffice\PhpSpreadsheet\Shared\Date;
use PHPUnit\Framework\TestCase;

class TimeValueTest extends TestCase
{
    protected function setUp(): void
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
    public function testTIMEVALUE($expectedResult, $timeValue): void
    {
        $result = DateTime::TIMEVALUE($timeValue);
        self::assertEqualsWithDelta($expectedResult, $result, 1E-8);
    }

    public function providerTIMEVALUE()
    {
        return require 'tests/data/Calculation/DateTime/TIMEVALUE.php';
    }

    public function testTIMEVALUEtoUnixTimestamp(): void
    {
        Functions::setReturnDateType(Functions::RETURNDATE_UNIX_TIMESTAMP);

        $result = DateTime::TIMEVALUE('7:30:20');
        self::assertEquals(23420, $result);
        self::assertEqualsWithDelta(23420, $result, 1E-8);
    }

    public function testTIMEVALUEtoDateTimeObject(): void
    {
        Functions::setReturnDateType(Functions::RETURNDATE_PHP_DATETIME_OBJECT);

        $result = DateTime::TIMEVALUE('7:30:20');
        //    Must return an object...
        self::assertIsObject($result);
        //    ... of the correct type
        self::assertTrue(is_a($result, 'DateTimeInterface'));
        //    ... with the correct value
        self::assertEquals($result->format('H:i:s'), '07:30:20');
    }
}
