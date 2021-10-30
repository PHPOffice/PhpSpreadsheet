<?php

namespace PhpOffice\PhpSpreadsheetTests\Worksheet;

use PhpOffice\PhpSpreadsheet\Cell\Cell;
use PhpOffice\PhpSpreadsheet\Worksheet\ColumnCellIterator;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

class ColumnCellIteratorTest extends TestCase
{
    /**
     * @var Worksheet&MockObject
     */
    private $mockWorksheet;

    /**
     * @var Cell&MockObject
     */
    private $mockCell;

    protected function setUp(): void
    {
        $this->mockCell = $this->getMockBuilder(Cell::class)
            ->disableOriginalConstructor()
            ->getMock();

        $this->mockWorksheet = $this->getMockBuilder(Worksheet::class)
            ->disableOriginalConstructor()
            ->getMock();

        $this->mockWorksheet->expects(self::any())
            ->method('getHighestRow')
            ->willReturn(5);
        $this->mockWorksheet->expects(self::any())
            ->method('getCellByColumnAndRow')
            ->willReturn($this->mockCell);
    }

    public function testIteratorFullRange(): void
    {
        $iterator = new ColumnCellIterator($this->mockWorksheet, 'A');
        $ColumnCellIndexResult = 1;
        self::assertEquals($ColumnCellIndexResult, $iterator->key());

        foreach ($iterator as $key => $ColumnCell) {
            self::assertEquals($ColumnCellIndexResult++, $key);
            self::assertInstanceOf(Cell::class, $ColumnCell);
        }
    }

    public function testIteratorStartEndRange(): void
    {
        $iterator = new ColumnCellIterator($this->mockWorksheet, 'A', 2, 4);
        $ColumnCellIndexResult = 2;
        self::assertEquals($ColumnCellIndexResult, $iterator->key());

        foreach ($iterator as $key => $ColumnCell) {
            self::assertEquals($ColumnCellIndexResult++, $key);
            self::assertInstanceOf(Cell::class, $ColumnCell);
        }
    }

    public function testIteratorSeekAndPrev(): void
    {
        $iterator = new ColumnCellIterator($this->mockWorksheet, 'A', 2, 4);
        $columnIndexResult = 4;
        $iterator->seek(4);
        self::assertEquals($columnIndexResult, $iterator->key());

        for ($i = 1; $i < $columnIndexResult - 1; ++$i) {
            $iterator->prev();
            self::assertEquals($columnIndexResult - $i, $iterator->key());
        }
    }

    public function testSeekOutOfRange(): void
    {
        $this->expectException(\PhpOffice\PhpSpreadsheet\Exception::class);
        $this->expectExceptionMessage('Row 1 is out of range');
        $iterator = new ColumnCellIterator($this->mockWorksheet, 'A', 2, 4);
        $iterator->seek(1);
    }

    public function testSeekNotExisting(): void
    {
        $this->expectException(\PhpOffice\PhpSpreadsheet\Exception::class);
        $this->expectExceptionMessage('Cell does not exist');

        $iterator = new ColumnCellIterator($this->mockWorksheet, 'A', 2, 4);
        $iterator->setIterateOnlyExistingCells(true);
        $iterator->seek(2);
    }

    public function testPrevOutOfRange(): void
    {
        $iterator = new ColumnCellIterator($this->mockWorksheet, 'A', 2, 4);
        $iterator->prev();
        self::assertFalse($iterator->valid());
    }
}
