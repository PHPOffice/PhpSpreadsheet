<?php

require_once 'testDataFileIterator.php';

class DefaultValueBinderTest extends PHPUnit_Framework_TestCase
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
     * @dataProvider providerDataTypeForValue
     */
	public function testDataTypeForValue()
	{
		$args = func_get_args();
		$expectedResult = array_pop($args);
		$result = call_user_func_array(array('PHPExcel_Cell_DefaultValueBinder','dataTypeForValue'),$args);
		$this->assertEquals($expectedResult, $result);
	}

    public function providerDataTypeForValue()
    {
    	return new testDataFileIterator('rawTestData/Cell/DefaultValueBinder.data');
	}

}
