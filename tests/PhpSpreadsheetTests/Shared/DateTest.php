<?php

namespace PhpSpreadsheetTests\Shared;

use PhpSpreadsheet\Shared\Date;

class DateTest extends \PHPUnit_Framework_TestCase
{
    public function testSetExcelCalendar()
    {
        $calendarValues = [
            Date::CALENDAR_MAC_1904,
            Date::CALENDAR_WINDOWS_1900,
        ];

        foreach ($calendarValues as $calendarValue) {
            $result = call_user_func([Date::class, 'setExcelCalendar'], $calendarValue);
            $this->assertTrue($result);
        }
    }

    public function testSetExcelCalendarWithInvalidValue()
    {
        $unsupportedCalendar = '2012';
        $result = call_user_func([Date::class, 'setExcelCalendar'], $unsupportedCalendar);
        $this->assertFalse($result);
    }

    /**
     * @dataProvider providerDateTimeExcelToTimestamp1900
     */
    public function testDateTimeExcelToTimestamp1900()
    {
        call_user_func(
            [Date::class, 'setExcelCalendar'],
            Date::CALENDAR_WINDOWS_1900
        );

        $args = func_get_args();
        $expectedResult = array_pop($args);
        $result = call_user_func_array([Date::class, 'excelToTimestamp'], $args);
        $this->assertEquals($expectedResult, $result);
    }

    public function providerDateTimeExcelToTimestamp1900()
    {
        return require 'data/Shared/Date/ExcelToTimestamp1900.php';
    }

    /**
     * @dataProvider providerDateTimeTimestampToExcel1900
     */
    public function testDateTimeTimestampToExcel1900()
    {
        call_user_func(
            [Date::class, 'setExcelCalendar'],
            Date::CALENDAR_WINDOWS_1900
        );

        $args = func_get_args();
        $expectedResult = array_pop($args);
        $result = call_user_func_array([Date::class, 'timestampToExcel'], $args);
        $this->assertEquals($expectedResult, $result, null, 1E-5);
    }

    public function providerDateTimeTimestampToExcel1900()
    {
        return require 'data/Shared/Date/TimestampToExcel1900.php';
    }

    /**
     * @dataProvider providerDateTimeDateTimeToExcel
     */
    public function testDateTimeDateTimeToExcel()
    {
        call_user_func(
            [Date::class, 'setExcelCalendar'],
            Date::CALENDAR_WINDOWS_1900
        );

        $args = func_get_args();
        $expectedResult = array_pop($args);
        $result = call_user_func_array([Date::class, 'dateTimeToExcel'], $args);
        $this->assertEquals($expectedResult, $result, null, 1E-5);
    }

    public function providerDateTimeDateTimeToExcel()
    {
        return require 'data/Shared/Date/DateTimeToExcel.php';
    }

    /**
     * @dataProvider providerDateTimeFormattedPHPToExcel1900
     */
    public function testDateTimeFormattedPHPToExcel1900()
    {
        call_user_func(
            [Date::class, 'setExcelCalendar'],
            Date::CALENDAR_WINDOWS_1900
        );

        $args = func_get_args();
        $expectedResult = array_pop($args);
        $result = call_user_func_array([Date::class, 'formattedPHPToExcel'], $args);
        $this->assertEquals($expectedResult, $result, null, 1E-5);
    }

    public function providerDateTimeFormattedPHPToExcel1900()
    {
        return require 'data/Shared/Date/FormattedPHPToExcel1900.php';
    }

    /**
     * @dataProvider providerDateTimeExcelToTimestamp1904
     */
    public function testDateTimeExcelToTimestamp1904()
    {
        call_user_func(
            [Date::class, 'setExcelCalendar'],
            Date::CALENDAR_MAC_1904
        );

        $args = func_get_args();
        $expectedResult = array_pop($args);
        $result = call_user_func_array([Date::class, 'excelToTimestamp'], $args);
        $this->assertEquals($expectedResult, $result);
    }

    public function providerDateTimeExcelToTimestamp1904()
    {
        return require 'data/Shared/Date/ExcelToTimestamp1904.php';
    }

    /**
     * @dataProvider providerDateTimeTimestampToExcel1904
     */
    public function testDateTimeTimestampToExcel1904()
    {
        call_user_func(
            [Date::class, 'setExcelCalendar'],
            Date::CALENDAR_MAC_1904
        );

        $args = func_get_args();
        $expectedResult = array_pop($args);
        $result = call_user_func_array([Date::class, 'timestampToExcel'], $args);
        $this->assertEquals($expectedResult, $result, null, 1E-5);
    }

    public function providerDateTimeTimestampToExcel1904()
    {
        return require 'data/Shared/Date/TimestampToExcel1904.php';
    }

    /**
     * @dataProvider providerIsDateTimeFormatCode
     */
    public function testIsDateTimeFormatCode()
    {
        $args = func_get_args();
        $expectedResult = array_pop($args);
        $result = call_user_func_array([Date::class, 'isDateTimeFormatCode'], $args);
        $this->assertEquals($expectedResult, $result);
    }

    public function providerIsDateTimeFormatCode()
    {
        return require 'data/Shared/Date/FormatCodes.php';
    }

    /**
     * @dataProvider providerDateTimeExcelToTimestamp1900Timezone
     */
    public function testDateTimeExcelToTimestamp1900Timezone()
    {
        call_user_func(
            [Date::class, 'setExcelCalendar'],
            Date::CALENDAR_WINDOWS_1900
        );

        $args = func_get_args();
        $expectedResult = array_pop($args);
        $result = call_user_func_array([Date::class, 'excelToTimestamp'], $args);
        $this->assertEquals($expectedResult, $result);
    }

    public function providerDateTimeExcelToTimestamp1900Timezone()
    {
        return require 'data/Shared/Date/ExcelToTimestamp1900Timezone.php';
    }
}
