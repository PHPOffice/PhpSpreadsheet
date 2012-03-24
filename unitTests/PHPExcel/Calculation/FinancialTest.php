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
		$this->assertEquals($expectedResult, $result, NULL, 1E-12);
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
		$this->assertEquals($expectedResult, $result, NULL, 1E-12);
	}

    public function providerACCRINTM()
    {
    	return new testDataFileIterator('rawTestData/Calculation/Financial/ACCRINTM.data');
	}

}
