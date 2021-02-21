<?php

namespace PhpOffice\PhpSpreadsheetTests\Calculation\Functions\DateTime;

use DateTimeImmutable;
use DateTimeInterface;
use PhpOffice\PhpSpreadsheet\Calculation\DateTime;
use PhpOffice\PhpSpreadsheet\Calculation\Functions;
use PhpOffice\PhpSpreadsheet\Shared\Date;
use PHPUnit\Framework\TestCase;

class DateValueTest extends TestCase
{
    private $returnDateType;

    private $excelCalendar;

    protected function setUp(): void
    {
        $this->returnDateType = Functions::getReturnDateType();
        $this->excelCalendar = Date::getExcelCalendar();
        Functions::setReturnDateType(Functions::RETURNDATE_EXCEL);
    }

    protected function tearDown(): void
    {
        Functions::setReturnDateType($this->returnDateType);
        Date::setExcelCalendar($this->excelCalendar);
    }

    /**
     * @dataProvider providerDATEVALUE
     *
     * @param mixed $expectedResult
     * @param $dateValue
     */
    public function testDATEVALUE($expectedResult, $dateValue): void
    {
        // Loop to avoid extraordinarily rare edge case where first calculation
        // and second do not take place on same day.
        do {
            $dtStart = new DateTimeImmutable();
            $startDay = $dtStart->format('d');
            if (is_string($expectedResult)) {
                $replYMD = str_replace('Y', date('Y'), $expectedResult);
                if ($replYMD !== $expectedResult) {
                    $expectedResult = DateTime::DATEVALUE($replYMD);
                }
            }
            $result = DateTime::DATEVALUE($dateValue);
            $dtEnd = new DateTimeImmutable();
            $endDay = $dtEnd->format('d');
        } while ($startDay !== $endDay);
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

    public function testDATEVALUEwith1904Calendar(): void
    {
        Date::setExcelCalendar(Date::CALENDAR_MAC_1904);
        self::assertEquals(5428, DateTime::DATEVALUE('1918-11-11'));
        self::assertEquals(0, DateTime::DATEVALUE('1904-01-01'));
        self::assertEquals('#VALUE!', DateTime::DATEVALUE('1903-12-31'));
        self::assertEquals('#VALUE!', DateTime::DATEVALUE('1900-02-29'));
    }
}
