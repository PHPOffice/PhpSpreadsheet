<?php


class ColumnTest extends PHPUnit_Framework_TestCase
{
	private $_testInitialColumn = 'H';

	private $_testAutoFilterColumnObject;


    public function setUp()
    {
        if (!defined('PHPEXCEL_ROOT')) {
            define('PHPEXCEL_ROOT', APPLICATION_PATH . '/');
        }
        require_once(PHPEXCEL_ROOT . 'PHPExcel/Autoloader.php');

		$this->_testAutoFilterColumnObject = new PHPExcel_Worksheet_AutoFilter_Column($this->_testInitialColumn);
    }

	public function testGetColumnIndex()
	{
		$result = $this->_testAutoFilterColumnObject->getColumnIndex();
		$this->assertEquals($this->_testInitialColumn, $result);
	}

	public function testSetColumnIndex()
	{
		$expectedResult = 'L';

		//	Setters return the instance to implement the fluent interface
		$result = $this->_testAutoFilterColumnObject->setColumnIndex($expectedResult);
		$this->assertInstanceOf('PHPExcel_Worksheet_AutoFilter_Column', $result);

		$result = $this->_testAutoFilterColumnObject->getColumnIndex();
		$this->assertEquals($expectedResult, $result);
	}

	public function testClone()
	{
		$result = clone $this->_testAutoFilterColumnObject;
		$this->assertInstanceOf('PHPExcel_Worksheet_AutoFilter_Column', $result);
	}

}
