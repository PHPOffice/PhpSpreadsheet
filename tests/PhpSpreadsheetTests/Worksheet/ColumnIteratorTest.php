<?php

namespace PhpOffice\PhpSpreadsheetTests\Worksheet;

use PhpOffice\PhpSpreadsheet\Worksheet\Column;
use PhpOffice\PhpSpreadsheet\Worksheet\ColumnIterator;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

class ColumnIteratorTest extends TestCase
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
            ->method('getHighestColumn')
            ->willReturn('E');
    }

    public function testIteratorFullRange(): void
    {
        $iterator = new ColumnIterator($this->mockWorksheet);
        $columnIndexResult = 'A';
        self::assertEquals($columnIndexResult, $iterator->key());

        foreach ($iterator as $key => $column) {
            self::assertEquals($columnIndexResult++, $key);
            self::assertInstanceOf(Column::class, $column);
        }
    }

    public function testIteratorStartEndRange(): void
    {
        $iterator = new ColumnIterator($this->mockWorksheet, 'B', 'D');
        $columnIndexResult = 'B';
        self::assertEquals($columnIndexResult, $iterator->key());

        foreach ($iterator as $key => $column) {
            self::assertEquals($columnIndexResult++, $key);
            self::assertInstanceOf(Column::class, $column);
        }
    }

    public function testIteratorSeekAndPrev(): void
    {
        $ranges = range('A', 'E');
        $iterator = new ColumnIterator($this->mockWorksheet, 'B', 'D');
        $columnIndexResult = 'D';
        $iterator->seek('D');
        self::assertEquals($columnIndexResult, $iterator->key());

        for ($i = 1; $i < array_search($columnIndexResult, $ranges); ++$i) {
            $iterator->prev();
            $expectedResult = $ranges[array_search($columnIndexResult, $ranges) - $i];
            self::assertEquals($expectedResult, $iterator->key());
        }
    }

    public function testIteratorResetStart(): void
    {
        $iterator = new ColumnIterator($this->mockWorksheet, 'B', 'D');
        $iterator->resetStart('E');

        $key = $iterator->key();
        self::assertSame('E', $key);

        $lastColumn = $iterator->key();
        while ($iterator->valid() !== false) {
            $iterator->next();
            $lastColumn = $iterator->key();
        }
        self::assertSame('F', $lastColumn);
    }

    public function testSeekOutOfRange(): void
    {
        $this->expectException(\PhpOffice\PhpSpreadsheet\Exception::class);

        $iterator = new ColumnIterator($this->mockWorksheet, 'B', 'D');
        $iterator->seek('A');
    }

    public function testPrevOutOfRange(): void
    {
        $iterator = new ColumnIterator($this->mockWorksheet, 'B', 'D');
        $iterator->prev();
        self::assertFalse($iterator->valid());
    }

    public function testResetStartOutOfRange(): void
    {
        $this->expectException(\PhpOffice\PhpSpreadsheet\Exception::class);

        $iterator = new ColumnIterator($this->mockWorksheet, 'B', 'D');
        $iterator->resetStart('H');
    }
}
