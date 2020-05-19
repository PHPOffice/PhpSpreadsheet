<?php

namespace PhpOffice\PhpSpreadsheetTests\Calculation\Functions\DateTime;

use PhpOffice\PhpSpreadsheet\Calculation\DateTime;
use PhpOffice\PhpSpreadsheet\Calculation\Functions;
use PhpOffice\PhpSpreadsheet\Shared\Date;
use PHPUnit\Framework\TestCase;

class EDateTest extends TestCase
{
    protected function setUp(): void
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
    public function testEDATE($expectedResult, $dateValue, $adjustmentMonths): void
    {
        $result = DateTime::EDATE($dateValue, $adjustmentMonths);
        self::assertEqualsWithDelta($expectedResult, $result, 1E-8);
    }

    public function providerEDATE()
    {
        return require 'tests/data/Calculation/DateTime/EDATE.php';
    }

    public function testEDATEtoUnixTimestamp(): void
    {
        Functions::setReturnDateType(Functions::RETURNDATE_UNIX_TIMESTAMP);

        $result = DateTime::EDATE('2012-1-26', -1);
        self::assertEquals(1324857600, $result);
        self::assertEqualsWithDelta(1324857600, $result, 1E-8);
    }

    public function testEDATEtoDateTimeObject(): void
    {
        Functions::setReturnDateType(Functions::RETURNDATE_PHP_DATETIME_OBJECT);

        $result = DateTime::EDATE('2012-1-26', -1);
        //    Must return an object...
        self::assertIsObject($result);
        //    ... of the correct type
        self::assertTrue(is_a($result, 'DateTimeInterface'));
        //    ... with the correct value
        self::assertEquals($result->format('d-M-Y'), '26-Dec-2011');
    }
}
