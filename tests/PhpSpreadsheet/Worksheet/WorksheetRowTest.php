<?php

namespace PhpSpreadsheet\Tests\Worksheet;

use PHPExcel\Worksheet;
use PHPExcel\Worksheet\Row;
use PHPExcel\Worksheet\RowCellIterator;

class WorksheetRowTest extends \PHPUnit_Framework_TestCase
{
    public $mockWorksheet;
    public $mockRow;

    public function setUp()
    {
        $this->mockWorksheet = $this->getMockBuilder(Worksheet::class)
            ->disableOriginalConstructor()
            ->getMock();
        $this->mockWorksheet->expects($this->any())
                 ->method('getHighestColumn')
                 ->will($this->returnValue('E'));
    }


    public function testInstantiateRowDefault()
    {
        $row = new Row($this->mockWorksheet);
        $this->assertInstanceOf(Row::class, $row);
        $rowIndex = $row->getRowIndex();
        $this->assertEquals(1, $rowIndex);
    }

    public function testInstantiateRowSpecified()
    {
        $row = new Row($this->mockWorksheet, 5);
        $this->assertInstanceOf(Row::class, $row);
        $rowIndex = $row->getRowIndex();
        $this->assertEquals(5, $rowIndex);
    }

    public function testGetCellIterator()
    {
        $row = new Row($this->mockWorksheet);
        $cellIterator = $row->getCellIterator();
        $this->assertInstanceOf(RowCellIterator::class, $cellIterator);
    }
}
