<?php

namespace PhpOffice\PhpSpreadsheetTests\Calculation\Functions\DateTime;

use PhpOffice\PhpSpreadsheet\Calculation\DateTime;
use PhpOffice\PhpSpreadsheet\Calculation\Functions;
use PhpOffice\PhpSpreadsheet\Shared\Date;
use PHPUnit\Framework\TestCase;

class DateTest extends TestCase
{
    protected function setUp(): void
    {
        Functions::setCompatibilityMode(Functions::COMPATIBILITY_EXCEL);
        Functions::setReturnDateType(Functions::RETURNDATE_EXCEL);
        Date::setExcelCalendar(Date::CALENDAR_WINDOWS_1900);
    }

    /**
     * @dataProvider providerDATE
     *
     * @param mixed $expectedResult
     * @param $year
     * @param $month
     * @param $day
     */
    public function testDATE($expectedResult, $year, $month, $day): void
    {
        $result = DateTime::DATE($year, $month, $day);
        self::assertEqualsWithDelta($expectedResult, $result, 1E-8);
    }

    public function providerDATE()
    {
        return require 'tests/data/Calculation/DateTime/DATE.php';
    }

    public function testDATEtoUnixTimestamp(): void
    {
        Functions::setReturnDateType(Functions::RETURNDATE_UNIX_TIMESTAMP);

        $result = DateTime::DATE(2012, 1, 31);
        self::assertEquals(1327968000, $result);
        self::assertEqualsWithDelta(1327968000, $result, 1E-8);
    }

    public function testDATEtoDateTimeObject(): void
    {
        Functions::setReturnDateType(Functions::RETURNDATE_PHP_DATETIME_OBJECT);

        $result = DateTime::DATE(2012, 1, 31);
        //    Must return an object...
        self::assertIsObject($result);
        //    ... of the correct type
        self::assertTrue(is_a($result, 'DateTimeInterface'));
        //    ... with the correct value
        self::assertEquals($result->format('d-M-Y'), '31-Jan-2012');
    }

    public function testDATEwith1904Calendar(): void
    {
        Date::setExcelCalendar(Date::CALENDAR_MAC_1904);

        $result = DateTime::DATE(1918, 11, 11);
        self::assertEquals($result, 5428);
    }

    public function testDATEwith1904CalendarError(): void
    {
        Date::setExcelCalendar(Date::CALENDAR_MAC_1904);

        $result = DateTime::DATE(1901, 1, 31);
        self::assertEquals($result, '#NUM!');
    }
}
