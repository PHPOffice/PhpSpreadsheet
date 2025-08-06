<?php

declare(strict_types=1);

namespace PhpOffice\PhpSpreadsheetTests\Shared;

use DateTime;
use DateTimeInterface;
use DateTimeZone;
use PhpOffice\PhpSpreadsheet\Exception;
use PhpOffice\PhpSpreadsheet\Shared\Date;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Style\NumberFormat;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

class DateTest extends TestCase
{
    private int $excelCalendar;

    private ?DateTimeZone $dttimezone;

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

        $spreadsheet = new Spreadsheet();
        foreach ($calendarValues as $calendarValue) {
            $result = Date::setExcelCalendar($calendarValue);
            self::assertTrue($result);
            $result = $spreadsheet->setExcelCalendar($calendarValue);
            self::assertTrue($result);
        }
        self::assertFalse($spreadsheet->setExcelCalendar(0));
        $spreadsheet->disconnectWorksheets();
    }

    public function testSetExcelCalendarWithInvalidValue(): void
    {
        $unsupportedCalendar = 2012;
        $result = Date::setExcelCalendar($unsupportedCalendar);
        self::assertFalse($result);
    }

    #[DataProvider('providerDateTimeExcelToTimestamp1900')]
    public function testDateTimeExcelToTimestamp1900(float|int $expectedResult, float|int $excelDateTimeValue): void
    {
        if ($expectedResult > PHP_INT_MAX || $expectedResult < PHP_INT_MIN) {
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

    #[DataProvider('providerDateTimeTimestampToExcel1900')]
    public function testDateTimeTimestampToExcel1900(float|int $expectedResult, float|int|string $unixTimestamp): void
    {
        Date::setExcelCalendar(Date::CALENDAR_WINDOWS_1900);

        $result = Date::timestampToExcel($unixTimestamp);
        self::assertEqualsWithDelta($expectedResult, $result, 1E-5);
    }

    public static function providerDateTimeTimestampToExcel1900(): array
    {
        return require 'tests/data/Shared/Date/TimestampToExcel1900.php';
    }

    #[DataProvider('providerDateTimeDateTimeToExcel')]
    public function testDateTimeDateTimeToExcel(float|int $expectedResult, DateTimeInterface $dateTimeObject): void
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
     * @param array{0: int, 1: int, 2: int, 3: int, 4: int, 5: float|int} $args Array containing year/month/day/hours/minutes/seconds
     */
    #[DataProvider('providerDateTimeFormattedPHPToExcel1900')]
    public function testDateTimeFormattedPHPToExcel1900(mixed $expectedResult, ...$args): void
    {
        Date::setExcelCalendar(Date::CALENDAR_WINDOWS_1900);

        $result = Date::formattedPHPToExcel(...$args); // @phpstan-ignore-line
        self::assertEqualsWithDelta($expectedResult, $result, 1E-5);
    }

    public static function providerDateTimeFormattedPHPToExcel1900(): array
    {
        return require 'tests/data/Shared/Date/FormattedPHPToExcel1900.php';
    }

    #[DataProvider('providerDateTimeExcelToTimestamp1904')]
    public function testDateTimeExcelToTimestamp1904(float|int $expectedResult, float|int $excelDateTimeValue): void
    {
        if ($expectedResult > PHP_INT_MAX || $expectedResult < PHP_INT_MIN) {
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

    #[DataProvider('providerDateTimeTimestampToExcel1904')]
    public function testDateTimeTimestampToExcel1904(mixed $expectedResult, float|int|string $unixTimestamp): void
    {
        Date::setExcelCalendar(Date::CALENDAR_MAC_1904);

        $result = Date::timestampToExcel($unixTimestamp);
        self::assertEqualsWithDelta($expectedResult, $result, 1E-5);
    }

    public static function providerDateTimeTimestampToExcel1904(): array
    {
        return require 'tests/data/Shared/Date/TimestampToExcel1904.php';
    }

    #[DataProvider('providerIsDateTimeFormatCode')]
    public function testIsDateTimeFormatCode(mixed $expectedResult, string $format): void
    {
        $result = Date::isDateTimeFormatCode($format);
        self::assertEquals($expectedResult, $result);
    }

    public static function providerIsDateTimeFormatCode(): array
    {
        return require 'tests/data/Shared/Date/FormatCodes.php';
    }

    #[DataProvider('providerDateTimeExcelToTimestamp1900Timezone')]
    public function testDateTimeExcelToTimestamp1900Timezone(float|int $expectedResult, float|int $excelDateTimeValue, string $timezone): void
    {
        if ($expectedResult > PHP_INT_MAX || $expectedResult < PHP_INT_MIN) {
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
        $timestamp1 = Date::stringToExcel('26.05.2025 14:28:00');
        $timestamp2 = Date::stringToExcel('26.05.2025 14:28:00.00');
        self::assertNotFalse($timestamp1);
        self::assertNotFalse($timestamp2);
        self::assertEqualsWithDelta($timestamp1, 45803.60277777778, 1.0E-10);
        self::assertSame($timestamp1, $timestamp2);

        $date = Date::PHPToExcel('2020-01-01');
        self::assertEquals(43831.0, $date);
        $phpDate = new DateTime('2020-01-02T00:00Z');
        $date = Date::PHPToExcel($phpDate);
        self::assertEquals(43832.0, $date);

        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setCellValue('B1', 'x');
        /** @var float|int|string */
        $val = $sheet->getCell('B1')->getValue();
        self::assertFalse(Date::timestampToExcel($val));

        $cell = $sheet->getCell('A1');

        $cell->setValue($date);
        $sheet->getStyle('A1')
            ->getNumberFormat()
            ->setFormatCode(NumberFormat::FORMAT_DATE_DATETIME);
        self::assertTrue(Date::isDateTime($cell));

        $cella2 = $sheet->getCell('A2');

        $cella2->setValue('=A1+2');
        $sheet->getStyle('A2')
            ->getNumberFormat()
            ->setFormatCode(NumberFormat::FORMAT_DATE_DATETIME);
        self::assertTrue(Date::isDateTime($cella2));

        $cella3 = $sheet->getCell('A3');

        $cella3->setValue('=A1+4');
        $sheet->getStyle('A3')
            ->getNumberFormat()
            ->setFormatCode('0.00E+00');
        self::assertFalse(Date::isDateTime($cella3));

        $cella4 = $sheet->getCell('A4');

        $cella4->setValue('= 44 7510557347');
        $sheet->getStyle('A4')
            ->getNumberFormat()
            ->setFormatCode('yyyy-mm-dd');
        self::assertFalse(Date::isDateTime($cella4));
        $spreadsheet->disconnectWorksheets();
    }

    public function testArray(): void
    {
        $spreadsheet = new Spreadsheet();
        $spreadsheet->returnArrayAsArray();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setCellValue('A1', 45000);
        $sheet->setCellValue('A2', 44000);
        $sheet->setCellValue('A3', 46000);
        $sheet->setCellValue('C1', '=SORT(A1:A3)');
        $sheet->setCellValue('D1', '=SORT(A1:A3)');
        $sheet->getStyle('C1')
            ->getNumberFormat()
            ->setFormatCode('yyyy-mm-dd');
        self::assertTrue(Date::isDateTime($sheet->getCell('C1')));
        self::assertFalse(Date::isDateTime($sheet->getCell('D1')));
        self::assertIsArray(
            $sheet->getCell('C1')->getCalculatedValue()
        );
        $spreadsheet->disconnectWorksheets();
    }

    public function testRoundMicroseconds(): void
    {
        $dti = new DateTime('2000-01-02 03:04:05.999999');
        Date::roundMicroseconds($dti);
        self::assertEquals(new DateTime('2000-01-02 03:04:06.000000'), $dti);

        $dti = new DateTime('2000-01-02 03:04:05.500000');
        Date::roundMicroseconds($dti);
        self::assertEquals(new DateTime('2000-01-02 03:04:06.000000'), $dti);

        $dti = new DateTime('2000-01-02 03:04:05.499999');
        Date::roundMicroseconds($dti);
        self::assertEquals(new DateTime('2000-01-02 03:04:05.000000'), $dti);
    }
}
