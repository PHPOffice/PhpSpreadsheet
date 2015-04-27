<?php

class WorksheetRowTest extends PHPUnit_Framework_TestCase
{
    public $mockWorksheet;
    public $mockRow;

	public function setUp()
	{
		if (!defined('PHPEXCEL_ROOT')) {
			define('PHPEXCEL_ROOT', APPLICATION_PATH . '/');
		}
		require_once(PHPEXCEL_ROOT . 'PHPExcel/Autoloader.php');
        
        $this->mockWorksheet = $this->getMockBuilder('PHPExcel_Worksheet')
            ->disableOriginalConstructor()
            ->getMock();
        $this->mockWorksheet->expects($this->any())
                 ->method('getHighestColumn')
                 ->will($this->returnValue('E'));
    }


	public function testInstantiateRowDefault()
	{
        $row = new PHPExcel_Worksheet_Row($this->mockWorksheet);
        $this->assertInstanceOf('PHPExcel_Worksheet_Row', $row);
        $rowIndex = $row->getRowIndex();
        $this->assertEquals(1, $rowIndex);
	}

	public function testInstantiateRowSpecified()
	{
        $row = new PHPExcel_Worksheet_Row($this->mockWorksheet, 5);
        $this->assertInstanceOf('PHPExcel_Worksheet_Row', $row);
        $rowIndex = $row->getRowIndex();
        $this->assertEquals(5, $rowIndex);
	}

	public function testGetCellIterator()
	{
        $row = new PHPExcel_Worksheet_Row($this->mockWorksheet);
        $cellIterator = $row->getCellIterator();
        $this->assertInstanceOf('PHPExcel_Worksheet_RowCellIterator', $cellIterator);
	}
}
