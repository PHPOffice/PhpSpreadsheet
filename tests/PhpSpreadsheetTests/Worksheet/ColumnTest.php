<?php

namespace PhpOffice\PhpSpreadsheetTests\Worksheet;

use PhpOffice\PhpSpreadsheet\Worksheet\Column;
use PhpOffice\PhpSpreadsheet\Worksheet\ColumnCellIterator;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

class ColumnTest extends TestCase
{
    /**
     * @var Worksheet&MockObject
     */
    private $mockWorksheet;

    protected function setUp(): void
    {
        $this->mockWorksheet = $this->getMockBuilder(Worksheet::class)
            ->disableOriginalConstructor()
            ->getMock();
        $this->mockWorksheet->expects(self::any())
            ->method('getHighestRow')
            ->willReturn(5);
    }

    public function testInstantiateColumnDefault(): void
    {
        $column = new Column($this->mockWorksheet);
        self::assertInstanceOf(Column::class, $column);
        $columnIndex = $column->getColumnIndex();
        self::assertEquals('A', $columnIndex);
    }

    public function testInstantiateColumnSpecified(): void
    {
        $column = new Column($this->mockWorksheet, 'E');
        self::assertInstanceOf(Column::class, $column);
        $columnIndex = $column->getColumnIndex();
        self::assertEquals('E', $columnIndex);
    }

    public function testGetCellIterator(): void
    {
        $column = new Column($this->mockWorksheet);
        $cellIterator = $column->getCellIterator();
        self::assertInstanceOf(ColumnCellIterator::class, $cellIterator);
    }
}
