<?php

namespace PhpOffice\PhpSpreadsheetTests\Worksheet;

use PhpOffice\PhpSpreadsheet\Worksheet\ColumnDimension;
use PHPUnit\Framework\TestCase;

class ColumnDimensionTest extends TestCase
{
    public function testInstantiateColumnDimensionDefault()
    {
        $expected = 'A';
        $columnDimension = new ColumnDimension();
        self::assertInstanceOf(ColumnDimension::class, $columnDimension);
        $result = $columnDimension->getColumnIndex();
        self::assertEquals($expected, $result);
    }

    public function testGetAndSetColumnIndex()
    {
        $expected = 'B';
        $columnDimension = new ColumnDimension();
        $columnDimension->setColumnIndex($expected);
        $result = $columnDimension->getColumnIndex();
        self::assertSame($expected, $result);
    }

    public function testGetAndSetWidth()
    {
        $expected = 1.2;
        $columnDimension = new ColumnDimension();
        $columnDimension->setWidth($expected);
        $result = $columnDimension->getWidth();
        self::assertSame($expected, $result);
    }

    public function testGetAndSetAutoSize()
    {
        $expected = true;
        $columnDimension = new ColumnDimension();
        $columnDimension->setAutoSize($expected);
        $result = $columnDimension->getAutoSize();
        self::assertSame($expected, $result);
    }
}
