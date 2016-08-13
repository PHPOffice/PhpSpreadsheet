<?php

namespace PhpSpreadsheet\Tests\Shared;

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
     * @dataProvider providerDateTimeExcelToPHP1900
     */
    public function testDateTimeExcelToPHP1900()
    {
        $result = call_user_func(
            array('\PHPExcel\Shared\Date','setExcelCalendar'),
            \PHPExcel\Shared\Date::CALENDAR_WINDOWS_1900
        );

        $args = func_get_args();
        $expectedResult = array_pop($args);
        if ($args[0] < 1) {
            $expectedResult += gmmktime(0, 0, 0);
        }
        $result = call_user_func_array(array('\PHPExcel\Shared\Date', 'ExcelToPHP'), $args);
        $this->assertEquals($expectedResult, $result);
    }

    public function providerDateTimeExcelToPHP1900()
    {
        return new \PhpSpreadsheet\Tests\TestDataFileIterator('rawTestData/Shared/DateTimeExcelToPHP1900.data');
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
        return new \PhpSpreadsheet\Tests\TestDataFileIterator('rawTestData/Shared/DateTimePHPToExcel1900.data');
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
        return new \PhpSpreadsheet\Tests\TestDataFileIterator('rawTestData/Shared/DateTimeFormattedPHPToExcel1900.data');
    }

    /**
     * @dataProvider providerDateTimeExcelToPHP1904
     */
    public function testDateTimeExcelToPHP1904()
    {
        $result = call_user_func(
            array('\PHPExcel\Shared\Date','setExcelCalendar'),
            \PHPExcel\Shared\Date::CALENDAR_MAC_1904
        );

        $args = func_get_args();
        $expectedResult = array_pop($args);
        if ($args[0] < 1) {
            $expectedResult += gmmktime(0, 0, 0);
        }
        $result = call_user_func_array(array('\PHPExcel\Shared\Date','ExcelToPHP'), $args);
        $this->assertEquals($expectedResult, $result);
    }

    public function providerDateTimeExcelToPHP1904()
    {
        return new \PhpSpreadsheet\Tests\TestDataFileIterator('rawTestData/Shared/DateTimeExcelToPHP1904.data');
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
        return new \PhpSpreadsheet\Tests\TestDataFileIterator('rawTestData/Shared/DateTimePHPToExcel1904.data');
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
        return new \PhpSpreadsheet\Tests\TestDataFileIterator('rawTestData/Shared/DateTimeFormatCodes.data');
    }

    /**
     * @dataProvider providerDateTimeExcelToPHP1900Timezone
     * @group fail19
     */
    public function testDateTimeExcelToPHP1900Timezone()
    {
        $result = call_user_func(
            array('\PHPExcel\Shared\Date','setExcelCalendar'),
            \PHPExcel\Shared\Date::CALENDAR_WINDOWS_1900
        );

        $args = func_get_args();
        $expectedResult = array_pop($args);
        if ($args[0] < 1) {
            $expectedResult += gmmktime(0, 0, 0);
        }
        $result = call_user_func_array(array('\PHPExcel\Shared\Date','ExcelToPHP'), $args);
        $this->assertEquals($expectedResult, $result);
    }

    public function providerDateTimeExcelToPHP1900Timezone()
    {
        return new \PhpSpreadsheet\Tests\TestDataFileIterator('rawTestData/Shared/DateTimeExcelToPHP1900Timezone.data');
    }
}
