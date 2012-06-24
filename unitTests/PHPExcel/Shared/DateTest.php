<?php


require_once 'testDataFileIterator.php';

class DateTest extends PHPUnit_Framework_TestCase
{

    public function setUp()
    {
        if (!defined('PHPEXCEL_ROOT')) {
            define('PHPEXCEL_ROOT', APPLICATION_PATH . '/');
        }
        require_once(PHPEXCEL_ROOT . 'PHPExcel/Autoloader.php');
	}

	public function testSetExcelCalendar()
	{
		$calendarValues = array(
			PHPExcel_Shared_Date::CALENDAR_MAC_1904,
			PHPExcel_Shared_Date::CALENDAR_WINDOWS_1900,
		);

		foreach($calendarValues as $calendarValue) {
			$result = call_user_func(array('PHPExcel_Shared_Date','setExcelCalendar'),$calendarValue);
			$this->assertTrue($result);
		}
	}

    public function testSetExcelCalendarWithInvalidValue()
	{
		$unsupportedCalendar = '2012';
		$result = call_user_func(array('PHPExcel_Shared_Date','setExcelCalendar'),$unsupportedCalendar);
		$this->assertFalse($result);
	}

    /**
     * @dataProvider providerDateTimeExcelToPHP1900
     */
	public function testDateTimeExcelToPHP1900()
	{
		$result = call_user_func(
			array('PHPExcel_Shared_Date','setExcelCalendar'),
			PHPExcel_Shared_Date::CALENDAR_WINDOWS_1900
		);

		$args = func_get_args();
		$expectedResult = array_pop($args);
		$result = call_user_func_array(array('PHPExcel_Shared_Date','ExcelToPHP'),$args);
		$this->assertEquals($expectedResult, $result);
	}

    public function providerDateTimeExcelToPHP1900()
    {
    	return new testDataFileIterator('rawTestData/Shared/DateTimeExcelToPHP1900.data');
	}

    /**
     * @dataProvider providerDateTimeExcelToPHP1904
     */
	public function testDateTimeExcelToPHP1904()
	{
		$result = call_user_func(
			array('PHPExcel_Shared_Date','setExcelCalendar'),
			PHPExcel_Shared_Date::CALENDAR_MAC_1904
		);

		$args = func_get_args();
		$expectedResult = array_pop($args);
		$result = call_user_func_array(array('PHPExcel_Shared_Date','ExcelToPHP'),$args);
		$this->assertEquals($expectedResult, $result);
	}

    public function providerDateTimeExcelToPHP1904()
    {
    	return new testDataFileIterator('rawTestData/Shared/DateTimeExcelToPHP1904.data');
	}

    /**
     * @dataProvider providerIsDateTimeFormatCode
     */
	public function testIsDateTimeFormatCode()
	{
		$args = func_get_args();
		$expectedResult = array_pop($args);
		$result = call_user_func_array(array('PHPExcel_Shared_Date','isDateTimeFormatCode'),$args);
		$this->assertEquals($expectedResult, $result);
	}

    public function providerIsDateTimeFormatCode()
    {
    	return new testDataFileIterator('rawTestData/Shared/DateTimeFormatCodes.data');
	}

}
