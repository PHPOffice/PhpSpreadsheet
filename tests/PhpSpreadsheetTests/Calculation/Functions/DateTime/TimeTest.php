<?php

namespace PhpOffice\PhpSpreadsheetTests\Calculation\Functions\DateTime;

use PhpOffice\PhpSpreadsheet\Calculation\DateTime;
use PhpOffice\PhpSpreadsheet\Calculation\Functions;
use PhpOffice\PhpSpreadsheet\Shared\Date;
use PHPUnit\Framework\TestCase;

class TimeTest extends TestCase
{
    protected function setUp(): void
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
    public function testTIME($expectedResult, ...$args): void
    {
        $result = DateTime::TIME(...$args);
        self::assertEqualsWithDelta($expectedResult, $result, 1E-8);
    }

    public function providerTIME()
    {
        return require 'tests/data/Calculation/DateTime/TIME.php';
    }

    public function testTIMEtoUnixTimestamp(): void
    {
        Functions::setReturnDateType(Functions::RETURNDATE_PHP_NUMERIC);

        $result = DateTime::TIME(7, 30, 20);
        self::assertEqualsWithDelta(27020, $result, 1E-8);
    }

    public function testTIMEtoDateTimeObject(): void
    {
        Functions::setReturnDateType(Functions::RETURNDATE_PHP_OBJECT);

        $result = DateTime::TIME(7, 30, 20);
        //    Must return an object...
        self::assertIsObject($result);
        //    ... of the correct type
        self::assertTrue(is_a($result, 'DateTimeInterface'));
        //    ... with the correct value
        self::assertEquals($result->format('H:i:s'), '07:30:20');
    }
}
