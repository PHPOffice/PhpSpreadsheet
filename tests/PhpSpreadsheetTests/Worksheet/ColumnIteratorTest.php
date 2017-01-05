<?php

namespace PhpOffice\PhpSpreadsheetTests\Worksheet;

use PhpOffice\PhpSpreadsheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Worksheet\Column;
use PhpOffice\PhpSpreadsheet\Worksheet\ColumnIterator;

class ColumnIteratorTest extends \PHPUnit_Framework_TestCase
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
        $this->mockWorksheet->expects($this->any())
            ->method('current')
            ->will($this->returnValue($this->mockColumn));
    }

    public function testIteratorFullRange()
    {
        $iterator = new ColumnIterator($this->mockWorksheet);
        $columnIndexResult = 'A';
        $this->assertEquals($columnIndexResult, $iterator->key());

        foreach ($iterator as $key => $column) {
            $this->assertEquals($columnIndexResult++, $key);
            $this->assertInstanceOf(Column::class, $column);
        }
    }

    public function testIteratorStartEndRange()
    {
        $iterator = new ColumnIterator($this->mockWorksheet, 'B', 'D');
        $columnIndexResult = 'B';
        $this->assertEquals($columnIndexResult, $iterator->key());

        foreach ($iterator as $key => $column) {
            $this->assertEquals($columnIndexResult++, $key);
            $this->assertInstanceOf(Column::class, $column);
        }
    }

    public function testIteratorSeekAndPrev()
    {
        $ranges = range('A', 'E');
        $iterator = new ColumnIterator($this->mockWorksheet, 'B', 'D');
        $columnIndexResult = 'D';
        $iterator->seek('D');
        $this->assertEquals($columnIndexResult, $iterator->key());

        for ($i = 1; $i < array_search($columnIndexResult, $ranges); ++$i) {
            $iterator->prev();
            $expectedResult = $ranges[array_search($columnIndexResult, $ranges) - $i];
            $this->assertEquals($expectedResult, $iterator->key());
        }
    }

    /**
     * @expectedException \PhpOffice\PhpSpreadsheet\Exception
     */
    public function testSeekOutOfRange()
    {
        $iterator = new ColumnIterator($this->mockWorksheet, 'B', 'D');
        $iterator->seek('A');
    }

    /**
     * @expectedException \PhpOffice\PhpSpreadsheet\Exception
     */
    public function testPrevOutOfRange()
    {
        $iterator = new ColumnIterator($this->mockWorksheet, 'B', 'D');
        $iterator->prev();
    }
}
