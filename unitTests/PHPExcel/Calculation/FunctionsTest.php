<?php


require_once 'testDataFileIterator.php';

class FunctionsTest extends PHPUnit_Framework_TestCase
{

    public function setUp()
    {
        if (!defined('PHPEXCEL_ROOT'))
        {
            define('PHPEXCEL_ROOT', APPLICATION_PATH . '/');
        }
        require_once(PHPEXCEL_ROOT . 'PHPExcel/Autoloader.php');
	}

	public function testDUMMY()
	{
		$result = PHPExcel_Calculation_Functions::DUMMY();
		$this->assertEquals('#Not Yet Implemented', $result);
	}

	public function testDIV0()
	{
		$result = PHPExcel_Calculation_Functions::DIV0();
		$this->assertEquals('#DIV/0!', $result);
	}

	public function testNA()
	{
		$result = PHPExcel_Calculation_Functions::NA();
		$this->assertEquals('#N/A', $result);
	}

	public function testNaN()
	{
		$result = PHPExcel_Calculation_Functions::NaN();
		$this->assertEquals('#NUM!', $result);
	}

	public function testNAME()
	{
		$result = PHPExcel_Calculation_Functions::NAME();
		$this->assertEquals('#NAME?', $result);
	}

	public function testREF()
	{
		$result = PHPExcel_Calculation_Functions::REF();
		$this->assertEquals('#REF!', $result);
	}

	public function testNULL()
	{
		$result = PHPExcel_Calculation_Functions::NULL();
		$this->assertEquals('#NULL!', $result);
	}

	public function testVALUE()
	{
		$result = PHPExcel_Calculation_Functions::VALUE();
		$this->assertEquals('#VALUE!', $result);
	}

}
