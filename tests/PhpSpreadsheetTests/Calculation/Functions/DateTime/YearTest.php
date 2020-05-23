<?php

namespace PhpOffice\PhpSpreadsheetTests\Calculation\Functions\DateTime;

use PhpOffice\PhpSpreadsheet\Calculation\DateTime;
use PhpOffice\PhpSpreadsheet\Calculation\Functions;
use PhpOffice\PhpSpreadsheet\Shared\Date;
use PHPUnit\Framework\TestCase;

class YearTest extends TestCase
{
    protected function setUp(): void
    {
        Functions::setCompatibilityMode(Functions::COMPATIBILITY_EXCEL);
        Functions::setReturnDateType(Functions::RETURNDATE_EXCEL);
        Date::setExcelCalendar(Date::CALENDAR_WINDOWS_1900);
    }

    /**
     * @dataProvider providerYEAR
     *
     * @param mixed $expectedResult
     * @param $dateTimeValue
     */
    public function testYEAR($expectedResult, $dateTimeValue): void
    {
        $result = DateTime::YEAR($dateTimeValue);
        self::assertEqualsWithDelta($expectedResult, $result, 1E-8);
    }

    public function providerYEAR()
    {
        return require 'tests/data/Calculation/DateTime/YEAR.php';
    }
}
