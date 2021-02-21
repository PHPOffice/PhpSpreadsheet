<?php

namespace PhpOffice\PhpSpreadsheetTests\Calculation\Functions\DateTime;

use PhpOffice\PhpSpreadsheet\Calculation\DateTime;
use PhpOffice\PhpSpreadsheet\Shared\Date;
use PHPUnit\Framework\TestCase;

class WeekDayTest extends TestCase
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
     * @dataProvider providerWEEKDAY
     *
     * @param mixed $expectedResult
     */
    public function testWEEKDAY($expectedResult, ...$args): void
    {
        $result = DateTime::WEEKDAY(...$args);
        self::assertEqualsWithDelta($expectedResult, $result, 1E-8);
    }

    public function providerWEEKDAY()
    {
        return require 'tests/data/Calculation/DateTime/WEEKDAY.php';
    }

    public function testWEEKDAYwith1904Calendar(): void
    {
        Date::setExcelCalendar(Date::CALENDAR_MAC_1904);
        self::assertEquals(7, DateTime::WEEKDAY('1904-01-02'));
        self::assertEquals(6, DateTime::WEEKDAY('1904-01-01'));
        self::assertEquals(6, DateTime::WEEKDAY(null));
    }
}
