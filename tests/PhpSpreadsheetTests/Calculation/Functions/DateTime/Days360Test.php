<?php

namespace PhpOffice\PhpSpreadsheetTests\Calculation\Functions\DateTime;

use PhpOffice\PhpSpreadsheet\Calculation\DateTime;
use PhpOffice\PhpSpreadsheet\Calculation\Functions;
use PhpOffice\PhpSpreadsheet\Shared\Date;
use PHPUnit\Framework\TestCase;

class Days360Test extends TestCase
{
    public function setUp()
    {
        Functions::setCompatibilityMode(Functions::COMPATIBILITY_EXCEL);
        Functions::setReturnDateType(Functions::RETURNDATE_EXCEL);
        Date::setExcelCalendar(Date::CALENDAR_WINDOWS_1900);
    }

    /**
     * @dataProvider providerDAYS360
     *
     * @param mixed $expectedResult
     */
    public function testDAYS360($expectedResult, ...$args)
    {
        $result = DateTime::DAYS360(...$args);
        $this->assertEquals($expectedResult, $result, '', 1E-8);
    }

    public function providerDAYS360()
    {
        return require 'data/Calculation/DateTime/DAYS360.php';
    }
}
