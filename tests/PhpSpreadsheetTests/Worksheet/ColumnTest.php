<?php

namespace PhpOffice\PhpSpreadsheetTests\Worksheet;

use PhpOffice\PhpSpreadsheet\Worksheet\Column;
use PhpOffice\PhpSpreadsheet\Worksheet\ColumnCellIterator;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PHPUnit\Framework\TestCase;

class ColumnTest extends TestCase
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
        self::assertInstanceOf(Column::class, $column);
        $columnIndex = $column->getColumnIndex();
        self::assertEquals('A', $columnIndex);
    }

    public function testInstantiateColumnSpecified()
    {
        $column = new Column($this->mockWorksheet, 'E');
        self::assertInstanceOf(Column::class, $column);
        $columnIndex = $column->getColumnIndex();
        self::assertEquals('E', $columnIndex);
    }

    public function testGetCellIterator()
    {
        $column = new Column($this->mockWorksheet);
        $cellIterator = $column->getCellIterator();
        self::assertInstanceOf(ColumnCellIterator::class, $cellIterator);
    }
}
