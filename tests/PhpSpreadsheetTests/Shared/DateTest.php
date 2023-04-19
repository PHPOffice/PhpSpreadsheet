<?php

namespace PhpOffice\PhpSpreadsheetTests\Shared;

use DateTimeZone;
use PhpOffice\PhpSpreadsheet\Exception;
use PhpOffice\PhpSpreadsheet\Shared\Date;
use PhpOffice\PhpSpreadsheet\Style\NumberFormat;
use PHPUnit\Framework\TestCase;

class DateTest extends TestCase
{
    /**
     * @var int
     */
    private $excelCalendar;

    /**
     * @var null|DateTimeZone
     */
    private $dttimezone;

    protected function setUp(): void
    {
        $this->dttimezone = Date::getDefaultTimeZoneOrNull();
        $this->excelCalendar = Date::getExcelCalendar();
    }

    protected function tearDown(): void
    {
        Date::setDefaultTimeZone($this->dttimezone);
        Date::setExcelCalendar($this->excelCalendar);
    }

    public function testSetExcelCalendar(): void
    {
        $calendarValues = [
            Date::CALENDAR_MAC_1904,
            Date::CALENDAR_WINDOWS_1900,
        ];

        foreach ($calendarValues as $calendarValue) {
            $result = Date::setExcelCalendar($calendarValue);
            self::assertTrue($result);
        }
    }

    public function testSetExcelCalendarWithInvalidValue(): void
    {
        $unsupportedCalendar = 2012;
        $result = Date::setExcelCalendar($unsupportedCalendar);
        self::assertFalse($result);
    }

    /**
     * @dataProvider providerDateTimeExcelToTimestamp1900
     *
     * @param mixed $expectedResult
     * @param mixed $excelDateTimeValue
     */
    public function testDateTimeExcelToTimestamp1900($expectedResult, $excelDateTimeValue): void
    {
        if (is_numeric($expectedResult) && ($expectedResult > PHP_INT_MAX || $expectedResult < PHP_INT_MIN)) {
            self::markTestSkipped('Test invalid on 32-bit system.');
        }
        Date::setExcelCalendar(Date::CALENDAR_WINDOWS_1900);

        $result = Date::excelToTimestamp($excelDateTimeValue);
        self::assertEquals($expectedResult, $result);
    }

    public static function providerDateTimeExcelToTimestamp1900(): array
    {
        return require 'tests/data/Shared/Date/ExcelToTimestamp1900.php';
    }

    /**
     * @dataProvider providerDateTimeTimestampToExcel1900
     *
     * @param mixed $expectedResult
     * @param mixed $unixTimestamp
     */
    public function testDateTimeTimestampToExcel1900($expectedResult, $unixTimestamp): void
    {
        Date::setExcelCalendar(Date::CALENDAR_WINDOWS_1900);

        $result = Date::timestampToExcel($unixTimestamp);
        self::assertEqualsWithDelta($expectedResult, $result, 1E-5);
    }

    public static function providerDateTimeTimestampToExcel1900(): array
    {
        return require 'tests/data/Shared/Date/TimestampToExcel1900.php';
    }

    /**
     * @dataProvider providerDateTimeDateTimeToExcel
     *
     * @param mixed $expectedResult
     * @param mixed $dateTimeObject
     */
    public function testDateTimeDateTimeToExcel($expectedResult, $dateTimeObject): void
    {
        Date::setExcelCalendar(Date::CALENDAR_WINDOWS_1900);

        $result = Date::dateTimeToExcel($dateTimeObject);
        self::assertEqualsWithDelta($expectedResult, $result, 1E-5);
    }

    public static function providerDateTimeDateTimeToExcel(): array
    {
        return require 'tests/data/Shared/Date/DateTimeToExcel.php';
    }

    /**
     * @dataProvider providerDateTimeFormattedPHPToExcel1900
     *
     * @param mixed $expectedResult
     */
    public function testDateTimeFormattedPHPToExcel1900($expectedResult, ...$args): void
    {
        Date::setExcelCalendar(Date::CALENDAR_WINDOWS_1900);

        $result = Date::formattedPHPToExcel(...$args);
        self::assertEqualsWithDelta($expectedResult, $result, 1E-5);
    }

    public static function providerDateTimeFormattedPHPToExcel1900(): array
    {
        return require 'tests/data/Shared/Date/FormattedPHPToExcel1900.php';
    }

    /**
     * @dataProvider providerDateTimeExcelToTimestamp1904
     *
     * @param mixed $expectedResult
     * @param mixed $excelDateTimeValue
     */
    public function testDateTimeExcelToTimestamp1904($expectedResult, $excelDateTimeValue): void
    {
        if (is_numeric($expectedResult) && ($expectedResult > PHP_INT_MAX || $expectedResult < PHP_INT_MIN)) {
            self::markTestSkipped('Test invalid on 32-bit system.');
        }
        Date::setExcelCalendar(Date::CALENDAR_MAC_1904);

        $result = Date::excelToTimestamp($excelDateTimeValue);
        self::assertEquals($expectedResult, $result);
    }

    public static function providerDateTimeExcelToTimestamp1904(): array
    {
        return require 'tests/data/Shared/Date/ExcelToTimestamp1904.php';
    }

    /**
     * @dataProvider providerDateTimeTimestampToExcel1904
     *
     * @param mixed $expectedResult
     * @param mixed $unixTimestamp
     */
    public function testDateTimeTimestampToExcel1904($expectedResult, $unixTimestamp): void
    {
        Date::setExcelCalendar(Date::CALENDAR_MAC_1904);

        $result = Date::timestampToExcel($unixTimestamp);
        self::assertEqualsWithDelta($expectedResult, $result, 1E-5);
    }

    public static function providerDateTimeTimestampToExcel1904(): array
    {
        return require 'tests/data/Shared/Date/TimestampToExcel1904.php';
    }

    /**
     * @dataProvider providerIsDateTimeFormatCode
     *
     * @param mixed $expectedResult
     */
    public function testIsDateTimeFormatCode($expectedResult, string $format): void
    {
        $result = Date::isDateTimeFormatCode($format);
        self::assertEquals($expectedResult, $result);
    }

    public static function providerIsDateTimeFormatCode(): array
    {
        return require 'tests/data/Shared/Date/FormatCodes.php';
    }

    /**
     * @dataProvider providerDateTimeExcelToTimestamp1900Timezone
     *
     * @param mixed $expectedResult
     * @param mixed $excelDateTimeValue
     * @param mixed $timezone
     */
    public function testDateTimeExcelToTimestamp1900Timezone($expectedResult, $excelDateTimeValue, $timezone): void
    {
        if (is_numeric($expectedResult) && ($expectedResult > PHP_INT_MAX || $expectedResult < PHP_INT_MIN)) {
            self::markTestSkipped('Test invalid on 32-bit system.');
        }
        Date::setExcelCalendar(Date::CALENDAR_WINDOWS_1900);

        $result = Date::excelToTimestamp($excelDateTimeValue, $timezone);
        self::assertEquals($expectedResult, $result);
    }

    public static function providerDateTimeExcelToTimestamp1900Timezone(): array
    {
        return require 'tests/data/Shared/Date/ExcelToTimestamp1900Timezone.php';
    }

    public function testConvertIsoDateError(): void
    {
        Date::setExcelCalendar(Date::CALENDAR_WINDOWS_1900);

        $this->expectException(Exception::class);
        $this->expectExceptionMessage('Non-string value supplied for Iso Date conversion');
        Date::convertIsoDate(false);
    }

    public function testVarious(): void
    {
        Date::setDefaultTimeZone('UTC');
        self::assertFalse(Date::stringToExcel('2019-02-29'));
        self::assertTrue((bool) Date::stringToExcel('2019-02-28'));
        self::assertTrue((bool) Date::stringToExcel('2019-02-28 11:18'));
        self::assertFalse(Date::stringToExcel('2019-02-28 11:71'));

        $date = Date::PHPToExcel('2020-01-01');
        self::assertEquals(43831.0, $date);

        $spreadsheet = new \PhpOffice\PhpSpreadsheet\Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setCellValue('B1', 'x');
        $val = $sheet->getCell('B1')->getValue();
        self::assertFalse(Date::timestampToExcel($val));

        $cell = $sheet->getCell('A1');
        self::assertNotNull($cell);

        $cell->setValue($date);
        $sheet->getStyle('A1')
            ->getNumberFormat()
            ->setFormatCode(NumberFormat::FORMAT_DATE_DATETIME);
        self::assertTrue(null !== $cell && Date::isDateTime($cell));

        $cella2 = $sheet->getCell('A2');
        self::assertNotNull($cella2);

        $cella2->setValue('=A1+2');
        $sheet->getStyle('A2')
            ->getNumberFormat()
            ->setFormatCode(NumberFormat::FORMAT_DATE_DATETIME);
        self::assertTrue(null !== $cella2 && Date::isDateTime($cella2));

        $cella3 = $sheet->getCell('A3');
        self::assertNotNull($cella3);

        $cella3->setValue('=A1+4');
        $sheet->getStyle('A3')
            ->getNumberFormat()
            ->setFormatCode('0.00E+00');
        self::assertFalse(null !== $cella3 && Date::isDateTime($cella3));

        $cella4 = $sheet->getCell('A4');
        self::assertNotNull($cella4);

        $cella4->setValue('= 44 7510557347');
        $sheet->getStyle('A4')
            ->getNumberFormat()
            ->setFormatCode('yyyy-mm-dd');
        self::assertFalse(Date::isDateTime($cella4));
    }
}
