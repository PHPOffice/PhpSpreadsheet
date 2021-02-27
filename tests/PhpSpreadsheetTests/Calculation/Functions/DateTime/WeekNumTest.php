<?php

namespace PhpOffice\PhpSpreadsheetTests\Calculation\Functions\DateTime;

use PhpOffice\PhpSpreadsheet\Calculation\DateTime;
use PhpOffice\PhpSpreadsheet\Shared\Date;
use PHPUnit\Framework\TestCase;

class WeekNumTest extends TestCase
{
    private $excelCalendar;

    protected function setUp(): void
    {
        $this->excelCalendar = Date::getExcelCalendar();
    }

    protected function tearDown(): void
    {
        Date::setExcelCalendar($this->excelCalendar);
    }

    /**
     * @dataProvider providerWEEKNUM
     *
     * @param mixed $expectedResult
     */
    public function testWEEKNUM($expectedResult, ...$args): void
    {
        $result = DateTime::WEEKNUM(...$args);
        self::assertEqualsWithDelta($expectedResult, $result, 1E-8);
    }

    public function providerWEEKNUM()
    {
        return require 'tests/data/Calculation/DateTime/WEEKNUM.php';
    }

    public function testWEEKNUMwith1904Calendar(): void
    {
        Date::setExcelCalendar(Date::CALENDAR_MAC_1904);
        self::assertEquals(27, DateTime::WEEKNUM('2004-07-02'));
        self::assertEquals(1, DateTime::WEEKNUM('1904-01-02'));
        self::assertEquals(1, DateTime::WEEKNUM(null));
        // The following is a bug in Excel.
        self::assertEquals(0, DateTime::WEEKNUM('1904-01-01'));
    }
}
