<?php

namespace PhpOffice\PhpSpreadsheetTests\Worksheet;

use PhpOffice\PhpSpreadsheet\Cell\Cell;
use PhpOffice\PhpSpreadsheet\Worksheet\RowCellIterator;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

class RowCellIteratorTest extends TestCase
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
            ->method('getHighestColumn')
            ->willReturn('E');
        $this->mockWorksheet->expects(self::any())
            ->method('getCellByColumnAndRow')
            ->willReturn($this->mockCell);
    }

    public function testIteratorFullRange(): void
    {
        $iterator = new RowCellIterator($this->mockWorksheet);
        $RowCellIndexResult = 'A';
        self::assertEquals($RowCellIndexResult, $iterator->key());

        foreach ($iterator as $key => $RowCell) {
            self::assertEquals($RowCellIndexResult++, $key);
            self::assertInstanceOf(Cell::class, $RowCell);
        }
    }

    public function testIteratorStartEndRange(): void
    {
        $iterator = new RowCellIterator($this->mockWorksheet, 2, 'B', 'D');
        $RowCellIndexResult = 'B';
        self::assertEquals($RowCellIndexResult, $iterator->key());

        foreach ($iterator as $key => $RowCell) {
            self::assertEquals($RowCellIndexResult++, $key);
            self::assertInstanceOf(Cell::class, $RowCell);
        }
    }

    public function testIteratorSeekAndPrev(): void
    {
        $ranges = range('A', 'E');
        $iterator = new RowCellIterator($this->mockWorksheet, 2, 'B', 'D');
        $RowCellIndexResult = 'D';
        $iterator->seek('D');
        self::assertEquals($RowCellIndexResult, $iterator->key());

        for ($i = 1; $i < array_search($RowCellIndexResult, $ranges); ++$i) {
            $iterator->prev();
            $expectedResult = $ranges[array_search($RowCellIndexResult, $ranges) - $i];
            self::assertEquals($expectedResult, $iterator->key());
        }
    }

    public function testSeekOutOfRange(): void
    {
        $this->expectException(\PhpOffice\PhpSpreadsheet\Exception::class);
        $this->expectExceptionMessage('Column A is out of range');

        $iterator = new RowCellIterator($this->mockWorksheet, 2, 'B', 'D');
        self::assertFalse($iterator->getIterateOnlyExistingCells());
        self::assertEquals(2, $iterator->getCurrentColumnIndex());
        $iterator->seek('A');
    }

    public function testSeekNotExisting(): void
    {
        $this->expectException(\PhpOffice\PhpSpreadsheet\Exception::class);
        $this->expectExceptionMessage('Cell does not exist');

        $iterator = new RowCellIterator($this->mockWorksheet, 2, 'B', 'D');
        $iterator->setIterateOnlyExistingCells(true);
        $iterator->seek('B');
    }

    public function testPrevOutOfRange(): void
    {
        $iterator = new RowCellIterator($this->mockWorksheet, 2, 'B', 'D');
        $iterator->prev();
        self::assertFalse($iterator->valid());
    }
}
