<?php

namespace PhpSpreadsheet\Tests\Worksheet;

class WorksheetRowTest extends \PHPUnit_Framework_TestCase
{
    public $mockWorksheet;
    public $mockRow;

    public function setUp()
    {
        $this->mockWorksheet = $this->getMockBuilder('\PHPExcel\Worksheet')
            ->disableOriginalConstructor()
            ->getMock();
        $this->mockWorksheet->expects($this->any())
                 ->method('getHighestColumn')
                 ->will($this->returnValue('E'));
    }


    public function testInstantiateRowDefault()
    {
        $row = new \PHPExcel\Worksheet\Row($this->mockWorksheet);
        $this->assertInstanceOf('\PHPExcel\Worksheet\Row', $row);
        $rowIndex = $row->getRowIndex();
        $this->assertEquals(1, $rowIndex);
    }

    public function testInstantiateRowSpecified()
    {
        $row = new \PHPExcel\Worksheet\Row($this->mockWorksheet, 5);
        $this->assertInstanceOf('\PHPExcel\Worksheet\Row', $row);
        $rowIndex = $row->getRowIndex();
        $this->assertEquals(5, $rowIndex);
    }

    public function testGetCellIterator()
    {
        $row = new \PHPExcel\Worksheet\Row($this->mockWorksheet);
        $cellIterator = $row->getCellIterator();
        $this->assertInstanceOf('\PHPExcel\Worksheet\RowCellIterator', $cellIterator);
    }
}
