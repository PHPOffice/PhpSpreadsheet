<?php

namespace PhpOffice\PhpSpreadsheetTests\Worksheet;

use PhpOffice\PhpSpreadsheet\Worksheet\Column;
use PhpOffice\PhpSpreadsheet\Worksheet\ColumnIterator;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PHPUnit\Framework\TestCase;

class ColumnIteratorTest extends TestCase
{
    public $mockWorksheet;

    public $mockColumn;

    public function setUp()
    {
        $this->mockColumn = $this->getMockBuilder(Column::class)
            ->disableOriginalConstructor()
            ->getMock();

        $this->mockWorksheet = $this->getMockBuilder(Worksheet::class)
            ->disableOriginalConstructor()
            ->getMock();

        $this->mockWorksheet->expects($this->any())
            ->method('getHighestColumn')
            ->will($this->returnValue('E'));
    }

    public function testIteratorFullRange()
    {
        $iterator = new ColumnIterator($this->mockWorksheet);
        $columnIndexResult = 'A';
        self::assertEquals($columnIndexResult, $iterator->key());

        foreach ($iterator as $key => $column) {
            self::assertEquals($columnIndexResult++, $key);
            self::assertInstanceOf(Column::class, $column);
        }
    }

    public function testIteratorStartEndRange()
    {
        $iterator = new ColumnIterator($this->mockWorksheet, 'B', 'D');
        $columnIndexResult = 'B';
        self::assertEquals($columnIndexResult, $iterator->key());

        foreach ($iterator as $key => $column) {
            self::assertEquals($columnIndexResult++, $key);
            self::assertInstanceOf(Column::class, $column);
        }
    }

    public function testIteratorSeekAndPrev()
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

    public function testSeekOutOfRange()
    {
        $this->expectException(\PhpOffice\PhpSpreadsheet\Exception::class);

        $iterator = new ColumnIterator($this->mockWorksheet, 'B', 'D');
        $iterator->seek('A');
    }

    public function testPrevOutOfRange()
    {
        $iterator = new ColumnIterator($this->mockWorksheet, 'B', 'D');
        $iterator->prev();
        self::assertFalse($iterator->valid());
    }
}
