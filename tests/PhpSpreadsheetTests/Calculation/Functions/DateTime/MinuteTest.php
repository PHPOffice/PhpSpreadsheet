<?php

namespace PhpOffice\PhpSpreadsheetTests\Calculation\Functions\DateTime;

use PhpOffice\PhpSpreadsheet\Calculation\DateTime;
use PhpOffice\PhpSpreadsheet\Calculation\Functions;
use PhpOffice\PhpSpreadsheet\Shared\Date;
use PHPUnit\Framework\TestCase;

class MinuteTest extends TestCase
{
    public function setUp()
    {
        Functions::setCompatibilityMode(Functions::COMPATIBILITY_EXCEL);
        Functions::setReturnDateType(Functions::RETURNDATE_EXCEL);
        Date::setExcelCalendar(Date::CALENDAR_WINDOWS_1900);
    }

    /**
     * @dataProvider providerMINUTE
     *
     * @param mixed $expectedResult
     */
    public function testMINUTE($expectedResult, ...$args)
    {
        $result = DateTime::MINUTE(...$args);
        $this->assertEquals($expectedResult, $result, '', 1E-8);
    }

    public function providerMINUTE()
    {
        return require 'data/Calculation/DateTime/MINUTE.php';
    }
}
