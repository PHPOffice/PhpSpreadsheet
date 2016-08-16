<?php

namespace PhpSpreadsheet\Tests\Worksheet;

use PhpSpreadsheet\Worksheet;
use PhpSpreadsheet\Worksheet\Column;
use PhpSpreadsheet\Worksheet\ColumnCellIterator;

class WorksheetColumnTest extends \PHPUnit_Framework_TestCase
{
    public $mockWorksheet;
    public $mockColumn;

    public function setUp()
    {
        $this->mockWorksheet = $this->getMockBuilder(Worksheet::class)
            ->disableOriginalConstructor()
            ->getMock();
        $this->mockWorksheet->expects($this->any())
                 ->method('getHighestRow')
                 ->will($this->returnValue(5));
    }


    public function testInstantiateColumnDefault()
    {
        $column = new Column($this->mockWorksheet);
        $this->assertInstanceOf(Column::class, $column);
        $columnIndex = $column->getColumnIndex();
        $this->assertEquals('A', $columnIndex);
    }

    public function testInstantiateColumnSpecified()
    {
        $column = new Column($this->mockWorksheet, 'E');
        $this->assertInstanceOf(Column::class, $column);
        $columnIndex = $column->getColumnIndex();
        $this->assertEquals('E', $columnIndex);
    }

    public function testGetCellIterator()
    {
        $column = new Column($this->mockWorksheet);
        $cellIterator = $column->getCellIterator();
        $this->assertInstanceOf(ColumnCellIterator::class, $cellIterator);
    }
}
