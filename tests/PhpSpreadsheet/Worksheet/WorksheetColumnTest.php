<?php

namespace PhpSpreadsheet\Tests\Worksheet;

class WorksheetColumnTest extends \PHPUnit_Framework_TestCase
{
    public $mockWorksheet;
    public $mockColumn;

    public function setUp()
    {
        $this->mockWorksheet = $this->getMockBuilder('\PHPExcel\Worksheet')
            ->disableOriginalConstructor()
            ->getMock();
        $this->mockWorksheet->expects($this->any())
                 ->method('getHighestRow')
                 ->will($this->returnValue(5));
    }


    public function testInstantiateColumnDefault()
    {
        $column = new \PHPExcel\Worksheet\Column($this->mockWorksheet);
        $this->assertInstanceOf('\PHPExcel\Worksheet\Column', $column);
        $columnIndex = $column->getColumnIndex();
        $this->assertEquals('A', $columnIndex);
    }

    public function testInstantiateColumnSpecified()
    {
        $column = new \PHPExcel\Worksheet\Column($this->mockWorksheet, 'E');
        $this->assertInstanceOf('\PHPExcel\Worksheet\Column', $column);
        $columnIndex = $column->getColumnIndex();
        $this->assertEquals('E', $columnIndex);
    }

    public function testGetCellIterator()
    {
        $column = new \PHPExcel\Worksheet\Column($this->mockWorksheet);
        $cellIterator = $column->getCellIterator();
        $this->assertInstanceOf('\PHPExcel\Worksheet\ColumnCellIterator', $cellIterator);
    }
}
