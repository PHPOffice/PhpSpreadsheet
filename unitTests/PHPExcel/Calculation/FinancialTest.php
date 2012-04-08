<?php


require_once 'testDataFileIterator.php';

class FinancialTest extends PHPUnit_Framework_TestCase
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
     * @dataProvider providerACCRINT
     */
	public function testACCRINT()
	{
		$args = func_get_args();
		$expectedResult = array_pop($args);
		$result = call_user_func_array(array('PHPExcel_Calculation_Financial','ACCRINT'),$args);
		$this->assertEquals($expectedResult, $result, NULL, 1E-8);
	}

    public function providerACCRINT()
    {
    	return new testDataFileIterator('rawTestData/Calculation/Financial/ACCRINT.data');
	}

    /**
     * @dataProvider providerACCRINTM
     */
	public function testACCRINTM()
	{
		$args = func_get_args();
		$expectedResult = array_pop($args);
		$result = call_user_func_array(array('PHPExcel_Calculation_Financial','ACCRINTM'),$args);
		$this->assertEquals($expectedResult, $result, NULL, 1E-8);
	}

    public function providerACCRINTM()
    {
    	return new testDataFileIterator('rawTestData/Calculation/Financial/ACCRINTM.data');
	}

    /**
     * @dataProvider providerAMORDEGRC
     */
	public function testAMORDEGRC()
	{
		$args = func_get_args();
		$expectedResult = array_pop($args);
		$result = call_user_func_array(array('PHPExcel_Calculation_Financial','AMORDEGRC'),$args);
		$this->assertEquals($expectedResult, $result, NULL, 1E-8);
	}

    public function providerAMORDEGRC()
    {
    	return new testDataFileIterator('rawTestData/Calculation/Financial/AMORDEGRC.data');
	}

    /**
     * @dataProvider providerAMORLINC
     */
	public function testAMORLINC()
	{
		$args = func_get_args();
		$expectedResult = array_pop($args);
		$result = call_user_func_array(array('PHPExcel_Calculation_Financial','AMORLINC'),$args);
		$this->assertEquals($expectedResult, $result, NULL, 1E-8);
	}

    public function providerAMORLINC()
    {
    	return new testDataFileIterator('rawTestData/Calculation/Financial/AMORLINC.data');
	}

    /**
     * @dataProvider providerCOUPDAYBS
     */
	public function testCOUPDAYBS()
	{
		$args = func_get_args();
		$expectedResult = array_pop($args);
		$result = call_user_func_array(array('PHPExcel_Calculation_Financial','COUPDAYBS'),$args);
		$this->assertEquals($expectedResult, $result, NULL, 1E-8);
	}

    public function providerCOUPDAYBS()
    {
    	return new testDataFileIterator('rawTestData/Calculation/Financial/COUPDAYBS.data');
	}

    /**
     * @dataProvider providerCOUPDAYS
     */
	public function testCOUPDAYS()
	{
		$args = func_get_args();
		$expectedResult = array_pop($args);
		$result = call_user_func_array(array('PHPExcel_Calculation_Financial','COUPDAYS'),$args);
		$this->assertEquals($expectedResult, $result, NULL, 1E-8);
	}

    public function providerCOUPDAYS()
    {
    	return new testDataFileIterator('rawTestData/Calculation/Financial/COUPDAYS.data');
	}

    /**
     * @dataProvider providerCOUPDAYSNC
     */
	public function testCOUPDAYSNC()
	{
		$args = func_get_args();
		$expectedResult = array_pop($args);
		$result = call_user_func_array(array('PHPExcel_Calculation_Financial','COUPDAYSNC'),$args);
		$this->assertEquals($expectedResult, $result, NULL, 1E-8);
	}

    public function providerCOUPDAYSNC()
    {
    	return new testDataFileIterator('rawTestData/Calculation/Financial/COUPDAYSNC.data');
	}

    /**
     * @dataProvider providerCOUPNCD
     */
	public function testCOUPNCD()
	{
		$args = func_get_args();
		$expectedResult = array_pop($args);
		$result = call_user_func_array(array('PHPExcel_Calculation_Financial','COUPNCD'),$args);
		$this->assertEquals($expectedResult, $result, NULL, 1E-8);
	}

    public function providerCOUPNCD()
    {
    	return new testDataFileIterator('rawTestData/Calculation/Financial/COUPNCD.data');
	}

    /**
     * @dataProvider providerCOUPNUM
     */
	public function testCOUPNUM()
	{
		$args = func_get_args();
		$expectedResult = array_pop($args);
		$result = call_user_func_array(array('PHPExcel_Calculation_Financial','COUPNUM'),$args);
		$this->assertEquals($expectedResult, $result, NULL, 1E-8);
	}

    public function providerCOUPNUM()
    {
    	return new testDataFileIterator('rawTestData/Calculation/Financial/COUPNUM.data');
	}

    /**
     * @dataProvider providerCOUPPCD
     */
	public function testCOUPPCD()
	{
		$args = func_get_args();
		$expectedResult = array_pop($args);
		$result = call_user_func_array(array('PHPExcel_Calculation_Financial','COUPPCD'),$args);
		$this->assertEquals($expectedResult, $result, NULL, 1E-8);
	}

    public function providerCOUPPCD()
    {
    	return new testDataFileIterator('rawTestData/Calculation/Financial/COUPPCD.data');
	}

    /**
     * @dataProvider providerCUMIPMT
     */
	public function testCUMIPMT()
	{
		$args = func_get_args();
		$expectedResult = array_pop($args);
		$result = call_user_func_array(array('PHPExcel_Calculation_Financial','CUMIPMT'),$args);
		$this->assertEquals($expectedResult, $result, NULL, 1E-8);
	}

    public function providerCUMIPMT()
    {
    	return new testDataFileIterator('rawTestData/Calculation/Financial/CUMIPMT.data');
	}

    /**
     * @dataProvider providerCUMPRINC
     */
	public function testCUMPRINC()
	{
		$args = func_get_args();
		$expectedResult = array_pop($args);
		$result = call_user_func_array(array('PHPExcel_Calculation_Financial','CUMPRINC'),$args);
		$this->assertEquals($expectedResult, $result, NULL, 1E-8);
	}

    public function providerCUMPRINC()
    {
    	return new testDataFileIterator('rawTestData/Calculation/Financial/CUMPRINC.data');
	}

    /**
     * @dataProvider providerDB
     */
	public function testDB()
	{
		$args = func_get_args();
		$expectedResult = array_pop($args);
		$result = call_user_func_array(array('PHPExcel_Calculation_Financial','DB'),$args);
		$this->assertEquals($expectedResult, $result, NULL, 1E-8);
	}

    public function providerDB()
    {
    	return new testDataFileIterator('rawTestData/Calculation/Financial/DB.data');
	}

    /**
     * @dataProvider providerDDB
     */
	public function testDDB()
	{
		$args = func_get_args();
		$expectedResult = array_pop($args);
		$result = call_user_func_array(array('PHPExcel_Calculation_Financial','DDB'),$args);
		$this->assertEquals($expectedResult, $result, NULL, 1E-8);
	}

    public function providerDDB()
    {
    	return new testDataFileIterator('rawTestData/Calculation/Financial/DDB.data');
	}

    /**
     * @dataProvider providerDISC
     */
	public function testDISC()
	{
		$args = func_get_args();
		$expectedResult = array_pop($args);
		$result = call_user_func_array(array('PHPExcel_Calculation_Financial','DISC'),$args);
		$this->assertEquals($expectedResult, $result, NULL, 1E-8);
	}

    public function providerDISC()
    {
    	return new testDataFileIterator('rawTestData/Calculation/Financial/DISC.data');
	}

    /**
     * @dataProvider providerDOLLARDE
     */
	public function testDOLLARDE()
	{
		$args = func_get_args();
		$expectedResult = array_pop($args);
		$result = call_user_func_array(array('PHPExcel_Calculation_Financial','DOLLARDE'),$args);
		$this->assertEquals($expectedResult, $result, NULL, 1E-8);
	}

    public function providerDOLLARDE()
    {
    	return new testDataFileIterator('rawTestData/Calculation/Financial/DOLLARDE.data');
	}

    /**
     * @dataProvider providerDOLLARFR
     */
	public function testDOLLARFR()
	{
		$args = func_get_args();
		$expectedResult = array_pop($args);
		$result = call_user_func_array(array('PHPExcel_Calculation_Financial','DOLLARFR'),$args);
		$this->assertEquals($expectedResult, $result, NULL, 1E-8);
	}

    public function providerDOLLARFR()
    {
    	return new testDataFileIterator('rawTestData/Calculation/Financial/DOLLARFR.data');
	}

    /**
     * @dataProvider providerRATE
     */
	public function testRATE()
	{
		$args = func_get_args();
		$expectedResult = array_pop($args);
		$result = call_user_func_array(array('PHPExcel_Calculation_Financial','RATE'),$args);
		$this->assertEquals($expectedResult, $result, NULL, 1E-8);
	}

    public function providerRATE()
    {
    	return new testDataFileIterator('rawTestData/Calculation/Financial/RATE.data');
	}

}
