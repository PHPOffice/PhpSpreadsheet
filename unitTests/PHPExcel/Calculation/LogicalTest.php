<?php


require_once 'testDataFileIterator.php';

class LogicalTest extends PHPUnit_Framework_TestCase
{

    public function setUp()
    {
        if (!defined('PHPEXCEL_ROOT'))
        {
            define('PHPEXCEL_ROOT', APPLICATION_PATH . '/');
        }
        require_once(PHPEXCEL_ROOT . 'PHPExcel/Autoloader.php');
	}

	public function testTRUE()
	{
		$result = PHPExcel_Calculation_Logical::TRUE();
		$this->assertEquals(TRUE, $result);
	}

	public function testFALSE()
	{
		$result = PHPExcel_Calculation_Logical::FALSE();
		$this->assertEquals(FALSE, $result);
	}

    /**
     * @dataProvider providerAND
     */
	public function testAND()
	{
		$args = func_get_args();
		$expectedResult = array_pop($args);
		$result = call_user_func_array(array('PHPExcel_Calculation_Logical','LOGICAL_AND'),$args);
		$this->assertEquals($expectedResult, $result);
	}

    public function providerAND()
    {
		return array(
				array(	'#VALUE!'	),								//	No arguments
				array(	NULL,	TRUE	),							//	NULL
				array(	TRUE,	NULL,	TRUE	),					//	Boolean TRUE and NULL
				array(	FALSE,	NULL,	FALSE	),					//	Boolean FALSE and NULL
				array(	TRUE,	TRUE,	TRUE	),					//	Both TRUE Booleans
				array(	TRUE,	FALSE,	FALSE	),					//	Mixed Booleans
				array(	FALSE,	TRUE,	FALSE	),					//	Mixed Booleans
				array(	FALSE,	FALSE,	FALSE	),					//	Both FALSE Booleans
				array(	TRUE,	TRUE,	FALSE,	FALSE	),			//	Multiple Mixed Booleans
				array(	TRUE,	TRUE,	TRUE,	TRUE	),			//	Multiple TRUE Booleans
				array(	FALSE,	FALSE,	FALSE,	FALSE,	FALSE	),	//	Multiple FALSE Booleans
				array(	-1,	-2,	TRUE	),
				array(	0,	0,	FALSE	),
				array(	0,	1,	FALSE	),
				array(	1,	1,	TRUE	),
				array(	'1',1,	'#VALUE!'	),
				array(	'TRUE',1,	TRUE	),						//	'TRUE' String
				array(	'FALSE',TRUE,	FALSE	),					//	'FALSE' String
				array(	'ABCD',1,	'#VALUE!'	),					//	Non-numeric String
				array(	-2,	1,	TRUE	),
				array(	-2,	0,	FALSE	),
			);

//    	return new testDataFileIterator('rawTestData/Calculation/Logical/AND.data');
	}

    /**
     * @dataProvider providerOR
     */
	public function testOR()
	{
		$args = func_get_args();
		$expectedResult = array_pop($args);
		$result = call_user_func_array(array('PHPExcel_Calculation_Logical','LOGICAL_OR'),$args);
		$this->assertEquals($expectedResult, $result);
	}

    public function providerOR()
    {
		return array(
				array(	'#VALUE!'	),								//	No arguments
				array(	NULL,	FALSE	),							//	NULL
				array(	TRUE,	NULL,	TRUE	),					//	Boolean TRUE and NULL
				array(	FALSE,	NULL,	FALSE	),					//	Boolean FALSE and NULL
				array(	TRUE,	TRUE,	TRUE	),					//	Both TRUE Booleans
				array(	TRUE,	FALSE,	TRUE	),					//	Mixed Booleans
				array(	FALSE,	TRUE,	TRUE	),					//	Mixed Booleans
				array(	FALSE,	FALSE,	FALSE	),					//	Both FALSE Booleans
				array(	TRUE,	TRUE,	FALSE,	TRUE	),			//	Multiple Mixed Booleans
				array(	TRUE,	TRUE,	TRUE,	TRUE	),			//	Multiple TRUE Booleans
				array(	FALSE,	FALSE,	FALSE,	FALSE,	FALSE	),	//	Multiple FALSE Booleans
				array(	-1,	-2,	TRUE	),
				array(	0,	0,	FALSE	),
				array(	0,	1,	TRUE	),
				array(	1,	1,	TRUE	),
				array(	'TRUE',1,	TRUE	),						//	'TRUE' String
				array(	'FALSE',TRUE,	TRUE	),					//	'FALSE' String
				array(	'ABCD',1,	'#VALUE!'	),					//	Non-numeric String
				array(	-2,	1,	TRUE	),
				array(	-2,	0,	TRUE	),
			);

//    	return new testDataFileIterator('rawTestData/Calculation/Logical/OR.data');
	}

    /**
     * @dataProvider providerNOT
     */
    public function testNOT()
	{
		$args = func_get_args();
		$expectedResult = array_pop($args);
		$result = call_user_func_array(array('PHPExcel_Calculation_Logical','NOT'),$args);
		$this->assertEquals($expectedResult, $result);
	}

    public function providerNOT()
    {
    	return new testDataFileIterator('rawTestData/Calculation/Logical/NOT.data');
    }

    /**
     * @dataProvider providerIF
     */
    public function testIF()
	{
		$args = func_get_args();
		$expectedResult = array_pop($args);
		$result = call_user_func_array(array('PHPExcel_Calculation_Logical','STATEMENT_IF'),$args);
		$this->assertEquals($expectedResult, $result);
	}

    public function providerIF()
    {
		return array(
				array(	0	),
				array(	TRUE,	0	),
				array(	FALSE,	FALSE	),
				array(	TRUE,	'ABC',	'ABC'	),
				array(	FALSE,	'ABC',	FALSE	),
				array(	TRUE,	'ABC',	'XYZ',	'ABC'	),
				array(	FALSE,	'ABC',	'XYZ',	'XYZ'	),
			);

//    	return new testDataFileIterator('rawTestData/Calculation/Logical/IF.data');
	}

    /**
     * @dataProvider providerIFERROR
     */
    public function testIFERROR()
	{
		$args = func_get_args();
		$expectedResult = array_pop($args);
		$result = call_user_func_array(array('PHPExcel_Calculation_Logical','IFERROR'),$args);
		$this->assertEquals($expectedResult, $result);
	}

    public function providerIFERROR()
    {
		return array(
				array(	TRUE,	'Not an Error',	'Not an Error'),
				array(	'#VALUE!',	'Error',	'Error'	),
			);

//    	return new testDataFileIterator('rawTestData/Calculation/Logical/IFERROR.data');
	}

}
