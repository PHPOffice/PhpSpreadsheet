<?php

namespace PhpOffice\PhpSpreadsheetTests\Calculation\Functions\DateTime;

use DateTimeInterface;
use PhpOffice\PhpSpreadsheet\Calculation\DateTime;
use PhpOffice\PhpSpreadsheet\Calculation\Functions;
use PhpOffice\PhpSpreadsheet\Shared\Date;
use PHPUnit\Framework\TestCase;

class DateValueTest extends TestCase
{
    protected function setUp(): void
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
    public function testDATEVALUE($expectedResult, $dateValue): void
    {
        $result = DateTime::DATEVALUE($dateValue);
        self::assertEqualsWithDelta($expectedResult, $result, 1E-8);
    }

    public function providerDATEVALUE()
    {
        return require 'tests/data/Calculation/DateTime/DATEVALUE.php';
    }

    public function testDATEVALUEtoUnixTimestamp(): void
    {
        Functions::setReturnDateType(Functions::RETURNDATE_UNIX_TIMESTAMP);

        $result = DateTime::DATEVALUE('2012-1-31');
        self::assertEquals(1327968000, $result);
        self::assertEqualsWithDelta(1327968000, $result, 1E-8);
    }

    public function testDATEVALUEtoDateTimeObject(): void
    {
        Functions::setReturnDateType(Functions::RETURNDATE_PHP_DATETIME_OBJECT);

        $result = DateTime::DATEVALUE('2012-1-31');
        //    Must return an object...
        self::assertIsObject($result);
        //    ... of the correct type
        self::assertTrue(is_a($result, DateTimeInterface::class));
        //    ... with the correct value
        self::assertEquals($result->format('d-M-Y'), '31-Jan-2012');
    }
}
