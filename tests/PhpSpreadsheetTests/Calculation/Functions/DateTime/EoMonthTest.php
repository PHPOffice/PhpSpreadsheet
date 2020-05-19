<?php

namespace PhpOffice\PhpSpreadsheetTests\Calculation\Functions\DateTime;

use PhpOffice\PhpSpreadsheet\Calculation\DateTime;
use PhpOffice\PhpSpreadsheet\Calculation\Functions;
use PhpOffice\PhpSpreadsheet\Shared\Date;
use PHPUnit\Framework\TestCase;

class EoMonthTest extends TestCase
{
    protected function setUp(): void
    {
        Functions::setCompatibilityMode(Functions::COMPATIBILITY_EXCEL);
        Functions::setReturnDateType(Functions::RETURNDATE_EXCEL);
        Date::setExcelCalendar(Date::CALENDAR_WINDOWS_1900);
    }

    /**
     * @dataProvider providerEOMONTH
     *
     * @param mixed $expectedResult
     * @param $dateValue
     * @param $adjustmentMonths
     */
    public function testEOMONTH($expectedResult, $dateValue, $adjustmentMonths): void
    {
        $result = DateTime::EOMONTH($dateValue, $adjustmentMonths);
        self::assertEqualsWithDelta($expectedResult, $result, 1E-8);
    }

    public function providerEOMONTH()
    {
        return require 'tests/data/Calculation/DateTime/EOMONTH.php';
    }

    public function testEOMONTHtoUnixTimestamp(): void
    {
        Functions::setReturnDateType(Functions::RETURNDATE_UNIX_TIMESTAMP);

        $result = DateTime::EOMONTH('2012-1-26', -1);
        self::assertEquals(1325289600, $result);
        self::assertEqualsWithDelta(1325289600, $result, 1E-8);
    }

    public function testEOMONTHtoDateTimeObject(): void
    {
        Functions::setReturnDateType(Functions::RETURNDATE_PHP_DATETIME_OBJECT);

        $result = DateTime::EOMONTH('2012-1-26', -1);
        //    Must return an object...
        self::assertIsObject($result);
        //    ... of the correct type
        self::assertTrue(is_a($result, 'DateTimeInterface'));
        //    ... with the correct value
        self::assertEquals($result->format('d-M-Y'), '31-Dec-2011');
    }
}
