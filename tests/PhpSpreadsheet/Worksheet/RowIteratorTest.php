<?php

namespace PhpSpreadsheet\Tests\Worksheet;

use PHPExcel\Worksheet\RowIterator;
use PHPExcel\Worksheet;
use PHPExcel\Worksheet\Row;

class RowIteratorTest extends \PHPUnit_Framework_TestCase
{
    public $mockWorksheet;
    public $mockRow;

    public function setUp()
    {
        $this->mockRow = $this->getMockBuilder(Row::class)
            ->disableOriginalConstructor()
            ->getMock();

        $this->mockWorksheet = $this->getMockBuilder(Worksheet::class)
            ->disableOriginalConstructor()
            ->getMock();

        $this->mockWorksheet->expects($this->any())
                 ->method('getHighestRow')
                 ->will($this->returnValue(5));
        $this->mockWorksheet->expects($this->any())
                 ->method('current')
                 ->will($this->returnValue($this->mockRow));
    }


    public function testIteratorFullRange()
    {
        $iterator = new RowIterator($this->mockWorksheet);
        $rowIndexResult = 1;
        $this->assertEquals($rowIndexResult, $iterator->key());

        foreach ($iterator as $key => $row) {
            $this->assertEquals($rowIndexResult++, $key);
            $this->assertInstanceOf(Row::class, $row);
        }
    }

    public function testIteratorStartEndRange()
    {
        $iterator = new RowIterator($this->mockWorksheet, 2, 4);
        $rowIndexResult = 2;
        $this->assertEquals($rowIndexResult, $iterator->key());

        foreach ($iterator as $key => $row) {
            $this->assertEquals($rowIndexResult++, $key);
            $this->assertInstanceOf(Row::class, $row);
        }
    }

    public function testIteratorSeekAndPrev()
    {
        $iterator = new RowIterator($this->mockWorksheet, 2, 4);
        $columnIndexResult = 4;
        $iterator->seek(4);
        $this->assertEquals($columnIndexResult, $iterator->key());

        for ($i = 1; $i < $columnIndexResult-1; $i++) {
            $iterator->prev();
            $this->assertEquals($columnIndexResult - $i, $iterator->key());
        }
    }

    /**
     * @expectedException \PHPExcel\Exception
     */
    public function testSeekOutOfRange()
    {
        $iterator = new RowIterator($this->mockWorksheet, 2, 4);
        $iterator->seek(1);
    }

    /**
     * @expectedException \PHPExcel\Exception
     */
    public function testPrevOutOfRange()
    {
        $iterator = new RowIterator($this->mockWorksheet, 2, 4);
        $iterator->prev();
    }
}
