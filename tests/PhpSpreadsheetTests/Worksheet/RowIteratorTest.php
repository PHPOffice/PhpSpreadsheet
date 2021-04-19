<?php

namespace PhpOffice\PhpSpreadsheetTests\Worksheet;

use PhpOffice\PhpSpreadsheet\Worksheet\Row;
use PhpOffice\PhpSpreadsheet\Worksheet\RowIterator;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

class RowIteratorTest extends TestCase
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

    public function testIteratorFullRange(): void
    {
        $iterator = new RowIterator($this->mockWorksheet);
        $rowIndexResult = 1;
        self::assertEquals($rowIndexResult, $iterator->key());

        foreach ($iterator as $key => $row) {
            self::assertEquals($rowIndexResult++, $key);
            self::assertInstanceOf(Row::class, $row);
        }
    }

    public function testIteratorStartEndRange(): void
    {
        $iterator = new RowIterator($this->mockWorksheet, 2, 4);
        $rowIndexResult = 2;
        self::assertEquals($rowIndexResult, $iterator->key());

        foreach ($iterator as $key => $row) {
            self::assertEquals($rowIndexResult++, $key);
            self::assertInstanceOf(Row::class, $row);
        }
    }

    public function testIteratorSeekAndPrev(): void
    {
        $iterator = new RowIterator($this->mockWorksheet, 2, 4);
        $columnIndexResult = 4;
        $iterator->seek(4);
        self::assertEquals($columnIndexResult, $iterator->key());

        for ($i = 1; $i < $columnIndexResult - 1; ++$i) {
            $iterator->prev();
            self::assertEquals($columnIndexResult - $i, $iterator->key());
        }
    }

    public function testIteratorResetStart(): void
    {
        $iterator = new RowIterator($this->mockWorksheet, 2, 4);
        $iterator->resetStart(5);

        $key = $iterator->key();
        self::assertSame(5, $key);

        $lastRow = $iterator->key();
        while ($iterator->valid() !== false) {
            $iterator->next();
            $lastRow = $iterator->key();
        }
        self::assertSame(6, $lastRow);
    }

    public function testSeekOutOfRange(): void
    {
        $this->expectException(\PhpOffice\PhpSpreadsheet\Exception::class);

        $iterator = new RowIterator($this->mockWorksheet, 2, 4);
        $iterator->seek(1);
    }

    public function testPrevOutOfRange(): void
    {
        $iterator = new RowIterator($this->mockWorksheet, 2, 4);
        $iterator->prev();
        self::assertFalse($iterator->valid());
    }

    public function testResetStartOutOfRange(): void
    {
        $this->expectException(\PhpOffice\PhpSpreadsheet\Exception::class);

        $iterator = new RowIterator($this->mockWorksheet, 2, 4);
        $iterator->resetStart(10);
    }
}
