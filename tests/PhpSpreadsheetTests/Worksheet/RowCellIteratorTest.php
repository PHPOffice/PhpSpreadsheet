<?php

namespace PhpOffice\PhpSpreadsheetTests\Worksheet;

use PhpOffice\PhpSpreadsheet\Cell\Cell;
use PhpOffice\PhpSpreadsheet\Worksheet\RowCellIterator;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PHPUnit\Framework\TestCase;

class RowCellIteratorTest extends TestCase
{
    public $mockWorksheet;

    public $mockCell;

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

        $iterator = new RowCellIterator($this->mockWorksheet, 2, 'B', 'D');
        $iterator->seek(1);
    }

    public function testPrevOutOfRange(): void
    {
        $iterator = new RowCellIterator($this->mockWorksheet, 2, 'B', 'D');
        $iterator->prev();
        self::assertFalse($iterator->valid());
    }
}
