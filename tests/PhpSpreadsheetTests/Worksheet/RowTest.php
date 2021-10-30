<?php

namespace PhpOffice\PhpSpreadsheetTests\Worksheet;

use PhpOffice\PhpSpreadsheet\Worksheet\Row;
use PhpOffice\PhpSpreadsheet\Worksheet\RowCellIterator;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

class RowTest extends TestCase
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

    public function testInstantiateRowDefault(): void
    {
        $row = new Row($this->mockWorksheet);
        self::assertInstanceOf(Row::class, $row);
        $rowIndex = $row->getRowIndex();
        self::assertEquals(1, $rowIndex);
    }

    public function testInstantiateRowSpecified(): void
    {
        $row = new Row($this->mockWorksheet, 5);
        self::assertInstanceOf(Row::class, $row);
        $rowIndex = $row->getRowIndex();
        self::assertEquals(5, $rowIndex);
    }

    public function testGetCellIterator(): void
    {
        $row = new Row($this->mockWorksheet);
        $cellIterator = $row->getCellIterator();
        self::assertInstanceOf(RowCellIterator::class, $cellIterator);
    }
}
