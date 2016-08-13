<?php

namespace PHPExcel\Shared;

require_once 'testDataFileIterator.php';

class DateTest extends \PHPUnit_Framework_TestCase
{
    public function testSetExcelCalendar()
    {
        $calendarValues = array(
            \PHPExcel\Shared\Date::CALENDAR_MAC_1904,
            \PHPExcel\Shared\Date::CALENDAR_WINDOWS_1900,
        );

        foreach ($calendarValues as $calendarValue) {
            $result = call_user_func(array('\PHPExcel\Shared\Date','setExcelCalendar'), $calendarValue);
            $this->assertTrue($result);
        }
    }

    public function testSetExcelCalendarWithInvalidValue()
    {
        $unsupportedCalendar = '2012';
        $result = call_user_func(array('\PHPExcel\Shared\Date','setExcelCalendar'), $unsupportedCalendar);
        $this->assertFalse($result);
    }

    /**
     * @dataProvider providerDateTimeExcelToTimestamp1900
     */
    public function testDateTimeExcelToTimestamp1900()
    {
        $result = call_user_func(
            array('\PHPExcel\Shared\Date','setExcelCalendar'),
            \PHPExcel\Shared\Date::CALENDAR_WINDOWS_1900
        );

        $args = func_get_args();
        $expectedResult = array_pop($args);
        $result = call_user_func_array(array('\PHPExcel\Shared\Date', 'excelToTimestamp'), $args);
        $this->assertEquals($expectedResult, $result);
    }

    public function providerDateTimeExcelToTimestamp1900()
    {
        return new \PhpSpreadhsheet\unitTests\TestDataFileIterator('rawTestData/Shared/DateTimeExcelToTimestamp1900.data');
    }

    /**
     * @dataProvider providerDateTimePHPToExcel1900
     */
    public function testDateTimePHPToExcel1900()
    {
        $result = call_user_func(
            array('\PHPExcel\Shared\Date','setExcelCalendar'),
            \PHPExcel\Shared\Date::CALENDAR_WINDOWS_1900
        );

        $args = func_get_args();
        $expectedResult = array_pop($args);
        $result = call_user_func_array(array('\PHPExcel\Shared\Date','PHPToExcel'), $args);
        $this->assertEquals($expectedResult, $result, null, 1E-5);
    }

    public function providerDateTimePHPToExcel1900()
    {
        return new \PhpSpreadhsheet\unitTests\TestDataFileIterator('rawTestData/Shared/DateTimePHPToExcel1900.data');
    }

    /**
     * @dataProvider providerDateTimeFormattedPHPToExcel1900
     */
    public function testDateTimeFormattedPHPToExcel1900()
    {
        $result = call_user_func(
            array('\PHPExcel\Shared\Date','setExcelCalendar'),
            \PHPExcel\Shared\Date::CALENDAR_WINDOWS_1900
        );

        $args = func_get_args();
        $expectedResult = array_pop($args);
        $result = call_user_func_array(array('\PHPExcel\Shared\Date','formattedPHPToExcel'), $args);
        $this->assertEquals($expectedResult, $result, null, 1E-5);
    }

    public function providerDateTimeFormattedPHPToExcel1900()
    {
        return new \PhpSpreadhsheet\unitTests\TestDataFileIterator('rawTestData/Shared/DateTimeFormattedPHPToExcel1900.data');
    }

    /**
     * @dataProvider providerDateTimeExcelToTimestamp1904
     */
    public function testDateTimeExcelToTimestamp1904()
    {
        $result = call_user_func(
            array('\PHPExcel\Shared\Date','setExcelCalendar'),
            \PHPExcel\Shared\Date::CALENDAR_MAC_1904
        );

        $args = func_get_args();
        $expectedResult = array_pop($args);
        $result = call_user_func_array(array('\PHPExcel\Shared\Date','excelToTimestamp'), $args);
        $this->assertEquals($expectedResult, $result);
    }

    public function providerDateTimeExcelToTimestamp1904()
    {
        return new \PhpSpreadhsheet\unitTests\TestDataFileIterator('rawTestData/Shared/DateTimeExcelToTimestamp1904.data');
    }

    /**
     * @dataProvider providerDateTimePHPToExcel1904
     */
    public function testDateTimePHPToExcel1904()
    {
        $result = call_user_func(
            array('\PHPExcel\Shared\Date','setExcelCalendar'),
            \PHPExcel\Shared\Date::CALENDAR_MAC_1904
        );

        $args = func_get_args();
        $expectedResult = array_pop($args);
        $result = call_user_func_array(array('\PHPExcel\Shared\Date','PHPToExcel'), $args);
        $this->assertEquals($expectedResult, $result, null, 1E-5);
    }

    public function providerDateTimePHPToExcel1904()
    {
        return new \PhpSpreadhsheet\unitTests\TestDataFileIterator('rawTestData/Shared/DateTimePHPToExcel1904.data');
    }

    /**
     * @dataProvider providerIsDateTimeFormatCode
     */
    public function testIsDateTimeFormatCode()
    {
        $args = func_get_args();
        $expectedResult = array_pop($args);
        $result = call_user_func_array(array('\PHPExcel\Shared\Date','isDateTimeFormatCode'), $args);
        $this->assertEquals($expectedResult, $result);
    }

    public function providerIsDateTimeFormatCode()
    {
        return new \PhpSpreadhsheet\unitTests\TestDataFileIterator('rawTestData/Shared/DateTimeFormatCodes.data');
    }

    /**
     * @dataProvider providerDateTimeExcelToTimestamp1900Timezone
     * @group fail19
     */
    public function testDateTimeExcelToTimestamp1900Timezone()
    {
        $result = call_user_func(
            array('\PHPExcel\Shared\Date','setExcelCalendar'),
            \PHPExcel\Shared\Date::CALENDAR_WINDOWS_1900
        );

        $args = func_get_args();
        $expectedResult = array_pop($args);
        $result = call_user_func_array(array('\PHPExcel\Shared\Date','excelToTimestamp'), $args);
        $this->assertEquals($expectedResult, $result);
    }

    public function providerDateTimeExcelToTimestamp1900Timezone()
    {
        return new \PhpSpreadhsheet\unitTests\TestDataFileIterator('rawTestData/Shared/DateTimeExcelToTimestamp1900Timezone.data');
    }
}
