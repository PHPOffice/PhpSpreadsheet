<?php

namespace PhpSpreadsheet\Tests\Worksheet;

use PhpSpreadsheet\Worksheet;
use PhpSpreadsheet\Cell;
use PhpSpreadsheet\Worksheet\RowCellIterator;

class RowCellIteratorTest extends \PHPUnit_Framework_TestCase
{
    public $mockWorksheet;
    public $mockRowCell;

    public function setUp()
    {
        $this->mockCell = $this->getMockBuilder(Cell::class)
            ->disableOriginalConstructor()
            ->getMock();

        $this->mockWorksheet = $this->getMockBuilder(Worksheet::class)
            ->disableOriginalConstructor()
            ->getMock();

        $this->mockWorksheet->expects($this->any())
                 ->method('getHighestColumn')
                 ->will($this->returnValue('E'));
        $this->mockWorksheet->expects($this->any())
                 ->method('getCellByColumnAndRow')
                 ->will($this->returnValue($this->mockCell));
    }


    public function testIteratorFullRange()
    {
        $iterator = new RowCellIterator($this->mockWorksheet);
        $RowCellIndexResult = 'A';
        $this->assertEquals($RowCellIndexResult, $iterator->key());

        foreach ($iterator as $key => $RowCell) {
            $this->assertEquals($RowCellIndexResult++, $key);
            $this->assertInstanceOf(Cell::class, $RowCell);
        }
    }

    public function testIteratorStartEndRange()
    {
        $iterator = new RowCellIterator($this->mockWorksheet, 2, 'B', 'D');
        $RowCellIndexResult = 'B';
        $this->assertEquals($RowCellIndexResult, $iterator->key());

        foreach ($iterator as $key => $RowCell) {
            $this->assertEquals($RowCellIndexResult++, $key);
            $this->assertInstanceOf(Cell::class, $RowCell);
        }
    }

    public function testIteratorSeekAndPrev()
    {
        $ranges = range('A', 'E');
        $iterator = new RowCellIterator($this->mockWorksheet, 2, 'B', 'D');
        $RowCellIndexResult = 'D';
        $iterator->seek('D');
        $this->assertEquals($RowCellIndexResult, $iterator->key());

        for ($i = 1; $i < array_search($RowCellIndexResult, $ranges); $i++) {
            $iterator->prev();
            $expectedResult = $ranges[array_search($RowCellIndexResult, $ranges) - $i];
            $this->assertEquals($expectedResult, $iterator->key());
        }
    }

    /**
     * @expectedException \PhpSpreadsheet\Exception
     */
    public function testSeekOutOfRange()
    {
        $iterator = new RowCellIterator($this->mockWorksheet, 2, 'B', 'D');
        $iterator->seek(1);
    }

    /**
     * @expectedException \PhpSpreadsheet\Exception
     */
    public function testPrevOutOfRange()
    {
        $iterator = new RowCellIterator($this->mockWorksheet, 2, 'B', 'D');
        $iterator->prev();
    }
}
