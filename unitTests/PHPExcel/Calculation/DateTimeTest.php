<?php


require_once 'testDataFileIterator.php';

class DateTimeTest extends PHPUnit_Framework_TestCase
{

    public function setUp()
    {
        if (!defined('PHPEXCEL_ROOT'))
        {
            define('PHPEXCEL_ROOT', APPLICATION_PATH . '/');
        }
        require_once(PHPEXCEL_ROOT . 'PHPExcel/Autoloader.php');
	}

    /**
     * @dataProvider providerDATE
     */
	public function testDATE()
	{
		$args = func_get_args();
		$expectedResult = array_pop($args);
		$result = call_user_func_array(array('PHPExcel_Calculation_DateTime','DATE'),$args);
		$this->assertEquals($expectedResult, $result, NULL, 1E-8);
	}

    public function providerDATE()
    {
    	return new testDataFileIterator('rawTestData/Calculation/DateTime/DATE.data');
	}

    /**
     * @dataProvider providerDATEVALUE
     */
	public function testDATEVALUE()
	{
		$args = func_get_args();
		$expectedResult = array_pop($args);
		$result = call_user_func_array(array('PHPExcel_Calculation_DateTime','DATEVALUE'),$args);
		$this->assertEquals($expectedResult, $result, NULL, 1E-8);
	}

    public function providerDATEVALUE()
    {
    	return new testDataFileIterator('rawTestData/Calculation/DateTime/DATEVALUE.data');
	}

    /**
     * @dataProvider providerYEAR
     */
	public function testYEAR()
	{
		$args = func_get_args();
		$expectedResult = array_pop($args);
		$result = call_user_func_array(array('PHPExcel_Calculation_DateTime','YEAR'),$args);
		$this->assertEquals($expectedResult, $result, NULL, 1E-8);
	}

    public function providerYEAR()
    {
    	return new testDataFileIterator('rawTestData/Calculation/DateTime/YEAR.data');
	}

    /**
     * @dataProvider providerMONTH
     */
	public function testMONTH()
	{
		$args = func_get_args();
		$expectedResult = array_pop($args);
		$result = call_user_func_array(array('PHPExcel_Calculation_DateTime','MONTHOFYEAR'),$args);
		$this->assertEquals($expectedResult, $result, NULL, 1E-8);
	}

    public function providerMONTH()
    {
    	return new testDataFileIterator('rawTestData/Calculation/DateTime/MONTH.data');
	}

    /**
     * @dataProvider providerWEEKNUM
     */
	public function testWEEKNUM()
	{
		$args = func_get_args();
		$expectedResult = array_pop($args);
		$result = call_user_func_array(array('PHPExcel_Calculation_DateTime','WEEKOFYEAR'),$args);
		$this->assertEquals($expectedResult, $result, NULL, 1E-8);
	}

    public function providerWEEKNUM()
    {
    	return new testDataFileIterator('rawTestData/Calculation/DateTime/WEEKNUM.data');
	}

    /**
     * @dataProvider providerWEEKDAY
     */
	public function testWEEKDAY()
	{
		$args = func_get_args();
		$expectedResult = array_pop($args);
		$result = call_user_func_array(array('PHPExcel_Calculation_DateTime','DAYOFWEEK'),$args);
		$this->assertEquals($expectedResult, $result, NULL, 1E-8);
	}

    public function providerWEEKDAY()
    {
    	return new testDataFileIterator('rawTestData/Calculation/DateTime/WEEKDAY.data');
	}

    /**
     * @dataProvider providerDAY
     */
	public function testDAY()
	{
		$args = func_get_args();
		$expectedResult = array_pop($args);
		$result = call_user_func_array(array('PHPExcel_Calculation_DateTime','DAYOFMONTH'),$args);
		$this->assertEquals($expectedResult, $result, NULL, 1E-8);
	}

    public function providerDAY()
    {
    	return new testDataFileIterator('rawTestData/Calculation/DateTime/DAY.data');
	}

    /**
     * @dataProvider providerTIME
     */
	public function testTIME()
	{
		$args = func_get_args();
		$expectedResult = array_pop($args);
		$result = call_user_func_array(array('PHPExcel_Calculation_DateTime','TIME'),$args);
		$this->assertEquals($expectedResult, $result, NULL, 1E-8);
	}

    public function providerTIME()
    {
    	return new testDataFileIterator('rawTestData/Calculation/DateTime/TIME.data');
	}

    /**
     * @dataProvider providerTIMEVALUE
     */
	public function testTIMEVALUE()
	{
		$args = func_get_args();
		$expectedResult = array_pop($args);
		$result = call_user_func_array(array('PHPExcel_Calculation_DateTime','TIMEVALUE'),$args);
		$this->assertEquals($expectedResult, $result, NULL, 1E-8);
	}

    public function providerTIMEVALUE()
    {
    	return new testDataFileIterator('rawTestData/Calculation/DateTime/TIMEVALUE.data');
	}

    /**
     * @dataProvider providerHOUR
     */
	public function testHOUR()
	{
		$args = func_get_args();
		$expectedResult = array_pop($args);
		$result = call_user_func_array(array('PHPExcel_Calculation_DateTime','HOUROFDAY'),$args);
		$this->assertEquals($expectedResult, $result, NULL, 1E-8);
	}

    public function providerHOUR()
    {
    	return new testDataFileIterator('rawTestData/Calculation/DateTime/HOUR.data');
	}

    /**
     * @dataProvider providerMINUTE
     */
	public function testMINUTE()
	{
		$args = func_get_args();
		$expectedResult = array_pop($args);
		$result = call_user_func_array(array('PHPExcel_Calculation_DateTime','MINUTEOFHOUR'),$args);
		$this->assertEquals($expectedResult, $result, NULL, 1E-8);
	}

    public function providerMINUTE()
    {
    	return new testDataFileIterator('rawTestData/Calculation/DateTime/MINUTE.data');
	}

    /**
     * @dataProvider providerSECOND
     */
	public function testSECOND()
	{
		$args = func_get_args();
		$expectedResult = array_pop($args);
		$result = call_user_func_array(array('PHPExcel_Calculation_DateTime','SECONDOFMINUTE'),$args);
		$this->assertEquals($expectedResult, $result, NULL, 1E-8);
	}

    public function providerSECOND()
    {
    	return new testDataFileIterator('rawTestData/Calculation/DateTime/SECOND.data');
	}

    /**
     * @dataProvider providerNETWORKDAYS
     */
	public function testNETWORKDAYS()
	{
		$args = func_get_args();
		$expectedResult = array_pop($args);
		$result = call_user_func_array(array('PHPExcel_Calculation_DateTime','NETWORKDAYS'),$args);
		$this->assertEquals($expectedResult, $result, NULL, 1E-8);
	}

    public function providerNETWORKDAYS()
    {
    	return new testDataFileIterator('rawTestData/Calculation/DateTime/NETWORKDAYS.data');
	}

    /**
     * @dataProvider providerEDATE
     */
	public function testEDATE()
	{
		$args = func_get_args();
		$expectedResult = array_pop($args);
		$result = call_user_func_array(array('PHPExcel_Calculation_DateTime','EDATE'),$args);
		$this->assertEquals($expectedResult, $result, NULL, 1E-8);
	}

    public function providerEDATE()
    {
    	return new testDataFileIterator('rawTestData/Calculation/DateTime/EDATE.data');
	}

    /**
     * @dataProvider providerEOMONTH
     */
	public function testEOMONTH()
	{
		$args = func_get_args();
		$expectedResult = array_pop($args);
		$result = call_user_func_array(array('PHPExcel_Calculation_DateTime','EOMONTH'),$args);
		$this->assertEquals($expectedResult, $result, NULL, 1E-8);
	}

    public function providerEOMONTH()
    {
    	return new testDataFileIterator('rawTestData/Calculation/DateTime/EOMONTH.data');
	}

}
